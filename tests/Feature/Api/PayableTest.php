<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use App\Models\AccountsScheduling;

class PayableTest extends TestCase
{
    protected $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testCreatePayableWhenUnauthenticatedUser()
    {
        $response = $this->postJson('/api/payables');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingAPayable()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $data = [
            'due_date'              => 'Invalid date',
            'description'           => 'De',
            'value'                 => -100,
            'category_id'           => 'Invalid category',
        ];

        $message_due_date               = __('validation.date_format', ['attribute' => 'due date', 'format' => 'Y-m-d']);
        $message_description            = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value                  = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_category_id            = __('validation.exists', ['attribute' => 'category id']);

        $response = $this->postJson('/api/payables', $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'due_date' =>[$message_due_date],
                    'description' =>[$message_description],
                    'value' =>[$message_value],
                    'category_id' =>[$message_category_id],
                ]);
    }

    public function testFailedWhenCreatePayableWithIncomeCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::INCOME]);

        $message = __('validation.exists', ['attribute' => 'category id']);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Payable test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->postJson('/api/payables', $data);

        $response->assertStatus(422)
            ->assertExactJson(['category_id' => [$message]]);

    }

    public function testCreatePayableWithInstallmentsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $data = [
            'due_date'              => now()->format('Y-m-d'),
            'description'           => 'Payable test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => 2,
            'installment_value'     => 50,
        ];

        $response = $this->postJson('/api/payables', $data);

        $response->assertStatus(201)
            ->assertJsonCount(2, 'data');
    }

    public function testValidationErrorWhenCreatePayableWithInstallments()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $data = [
            'due_date'              => now()->format('Y-m-d'),
            'description'           => 'Payable test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => -2,
            'installment_value'     => -100,
        ];

        $message_installments_number    = __('validation.gt.numeric', ['attribute' => 'installments number', 'value' => 0]);
        $message_installments_value     = __('validation.gt.numeric', ['attribute' => 'installment value', 'value' => 0]);

        $response = $this->postJson('/api/payables', $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'installment_value' => [$message_installments_value],
                'installments_number' => [$message_installments_number]
            ]);            
    }

    public function testCreatePayableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Payable test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->postJson('/api/payables', $data);

        $response->assertStatus(201)
            ->assertJsonPath('due_date', $data['due_date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value'])
            ->assertJsonPath('category.id', $category->id);
    }

    public function testGetPayablesWithUnauthenticatedUser()
    {
        $response = $this->getJson('api/payables');

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetPayablesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);
        
        factory(AccountsScheduling::class, 2)->create(['category_id' => $category->id]);

        $response = $this->getJson('api/payables');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testGetAPayableByRangeDate()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $older_payable = factory(AccountsScheduling::class)->create([
            'due_date'      => now()->format('Y-m-01'),
            'category_id'   => $category->id
        ]);

        $new_payable = factory(AccountsScheduling::class)->create([
            'due_date'      => now()->format('Y-m-15'),
            'category_id'   => $category->id
        ]);

        $response = $this->getJson("/api/payables?from={$new_payable->due_date}&to={$new_payable->due_date}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testGetNonExistentPayable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $payable = 'Invalid Payable';

        $message = __('messages.account_scheduling.api_not_found');

        $response = $this->getJson("api/payables/{$payable}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetPayableFromAnotherUser()
    {
        $testUser = factory(User::class)->create();

        $payable = AccountsScheduling::withoutEvents(function () use ($testUser) {
            return factory(AccountsScheduling::class)->create([
                'user_id' => $testUser->id
            ]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/payables/{$payable->id}");

        $response->assertStatus(404);
    }

    public function testGetAPayableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);        

        $response = $this->getJson("/api/payables/{$payable->id}");

        $response->assertStatus(200)
                ->assertJsonPath('id', $payable->id)
                ->assertJsonPath('name', $payable->name);
    }

    public function testUpdatePayableWithUnauthenticatedUser()
    {
        $payable = 'payable';

        $response = $this->putJson("/api/payables/{$payable}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentPayable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $payable = 'payable';

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Update test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->putJson("/api/payables/{$payable}", $data);

        $response->assertStatus(404);
    }

    public function testValidationErrorWhenUpdateAPayable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $payable = factory(AccountsScheduling::class)->create();
        
        $data = [
            'due_date'      => 'Invalid date',
            'description'   => 'De',
            'value'         => 'Invalid value',
            'category_id'   => 'Invalid category',
        ];

        $message_due_date               = __('validation.date_format', ['attribute' => 'due date', 'format' => 'Y-m-d']);
        $message_description            = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value                  = __('validation.numeric', ['attribute' => 'value']);
        $message_category_id            = __('validation.exists', ['attribute' => 'category id']);


        $response = $this->putJson("/api/payables/{$payable->id}", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'due_date' =>[$message_due_date],
                'description' =>[$message_description],
                'value' =>[$message_value],
                'category_id' =>[$message_category_id],
            ]);
    }

    public function testUpdateAPayableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable  = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);

        $data = [
            'due_date'      => now()->modify('+7 days')->format('Y-m-d'),
            'description'   => 'Payable updated',
            'value'         => 200,
            'category_id'   => (factory(Category::class)->create(['type' => Category::EXPENSE]))->id
        ];

        $response = $this->putJson("/api/payables/{$payable->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('due_date', $data['due_date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value'])
            ->assertJsonPath('category.id', $data['category_id']);
    }

    public function testDeletePayableWithUnauthenticatedUser()
    {
        $payable = 'payable';

        $response = $this->putJson("/api/payables/{$payable}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testDeleteNonExistentPayable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $payable = 'payable';

        $response = $this->deleteJson("/api/payables/{$payable}");

        $response->assertStatus(404);
    }

    public function testFailedWhenDeletePayableWithInvalidCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::INCOME]);

        $payable = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);

        $message = __('messages.account_scheduling.not_payable');

        $response = $this->deleteJson("/api/payables/{$payable->id}");

        $response->assertStatus(422)
        ->assertExactJson([
            'message' =>$message,
        ]);
    }

    public function testDeletePayableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/payables/{$payable->id}");

        $response->assertStatus(204);
    }

    public function testPaymentSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable  = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);

        $data = [
            'paid_date'     => now()->modify('+1 days')->format('Y-m-d'),
            'account_id'    => $account->id,
            'value'         => $payable->value
        ];

        $response = $this->postJson("/api/payables/{$payable->id}/payment", $data);

        $response->assertStatus(200)
            ->assertExactJson([
                'message' =>__('messages.account_scheduling.payable_paid'),
            ]);
    }

    public function testValidationErrorWhenMakingPayment()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable  = factory(AccountsScheduling::class)->create(['category_id' => $category->id]);

        $message_paid_date  = __('validation.date_format', ['attribute' => 'paid date', 'format' => 'Y-m-d']);
        $message_account_id = __('validation.exists', ['attribute' => 'account id']);
        $message_value      = __('validation.filled', ['attribute' => 'value']);

        $data = [
            'paid_date'     => 'Invalid date',
            'account_id'    => 'Invalid account'
        ];

        $response = $this->postJson("/api/payables/{$payable}/payment", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'paid_date'     => [$message_paid_date],
                'account_id'    => [$message_account_id],
                'value'         => [$message_value]
            ]);
    }

    public function testCancelPaymentSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();
        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $payable = factory(AccountsScheduling::class)->create([
            'category_id'   => $category->id,
            'paid_date'     => now(),
            'paid'          => true
        ]);
        
        $entry = factory(AccountEntry::class)->create([
            'date'                  => $payable->paid_date,
            'value'                 => $payable->value,
            'account_id'            => $account->id,
            'category_id'           => $payable->category_id,
            'account_scheduling_id' => $payable->id
        ]);

        $response = $this->postJson("/api/payables/{$payable->id}/cancel-payment");

        $response->assertStatus(200)
            ->assertExactJson([
                'message' =>__('messages.account_scheduling.payable_cancel'),
            ]);
    }

    public function testFailedWhenCancelNonExistentPayable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $payable = 'Payable';

        $response = $this->postJson("/api/payables/{$payable}/cancel-payment");

        $response->assertStatus(404);
    }
}
