<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Card;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    protected $user;
    protected $invalidData;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->invalidData = [
            "description"               => "d",
            "date"                      => "20210509",
            "value"                     => "Test",
            "account_id"                => "9999",
            "card_id"                   => "9999",
            "income_category_id"        => "9999",
            "expense_category_id"   => "9999",
        ];
    }
    
    public function testCreatePartialPaymentWhenUnauthenticatedUser()
    {
        $response = $this->postJson("/api/cards/invoices/partial-payment", []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatePartialPayment()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $message_date                       = __('validation.date_format', ['attribute' => __('validation.attributes.date'), 'format' => 'Y-m-d']);
        $message_description                = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value_gt_zero              = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_value_numeric              = __('validation.numeric', ['attribute' => 'value']);
        $message_income_category_id         = __('validation.exists', ['attribute' => 'income category id']);
        $message_expense_category_id        = __('validation.exists', ['attribute' => 'expense category id']);
        $message_account_id                 = __('validation.exists', ['attribute' => 'account id']);
        $message_card_id                    = __('validation.exists', ['attribute' => 'card id']);

        $response = $this->postJson("/api/cards/invoices/partial-payment", $this->invalidData);

        $response->assertStatus(422)
            ->assertExactJson([
                'description'               => [$message_description],
                'date'                      => [$message_date],
                'value'                     => [$message_value_numeric, $message_value_gt_zero],
                'income_category_id'        => [$message_income_category_id],
                'expense_category_id'       => [$message_expense_category_id],
                'account_id'                => [$message_account_id],
                'card_id'                   => [$message_card_id]
            ]);

    }

    public function testErrorWhenMakingAPartialPaymentWithInvalidCategories()
    {
        Sanctum::actingAs(
            $this->user
        );       

        $data = $this->getData();
        
        $income_category = Category::factory()->create(['type' => Category::INCOME]);
        $expense_category = Category::factory()->create(['type' => Category::EXPENSE]);
        
        $data['income_category_id']   = $expense_category->id;
        $data['expense_category_id']  = $income_category->id;

        $message_income_category_id     = __('validation.exists', ['attribute' => 'income category id']);
        $message_expense_category_id    = __('validation.exists', ['attribute' => 'expense category id']);

        $response = $this->postJson("/api/cards/invoices/partial-payment", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'income_category_id'        => [$message_income_category_id],
                'expense_category_id'       => [$message_expense_category_id],
            ]);

    }

    public function testCreatePartialPaymentSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = $this->getData();
        
        $response = $this->postJson("/api/cards/invoices/partial-payment", $data);

        $response->assertStatus(200);
    }

    private function getData() {
        $account                = Account::factory()->create(['user_id' => $this->user->id]);
        $card                   = Card::factory()->create(['user_id' => $this->user->id]);
        $income_category        = Category::factory()->create(['type' => Category::INCOME]);
        $expense_category       = Category::factory()->create(['type' => Category::EXPENSE]);

        return [
            "description"               => "Test description",
            "date"                      => now()->format('Y-m-d'),
            "value"                     => 100,
            "account_id"                => $account->id,
            "card_id"                   => $card->id,
            "income_category_id"        => $income_category->id,
            "expense_category_id"       => $expense_category->id,
        ];
    }
}