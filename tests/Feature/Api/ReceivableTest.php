<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\AccountEntry;
use Laravel\Sanctum\Sanctum;
use App\Models\AccountsScheduling;

class ReceivableTest extends TestCase
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

        $this->user = User::factory()->create();
    }

    public function testCreateReceivableWhenUnauthenticatedUser()
    {
        $response = $this->postJson('/api/receivables');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingAReceivable()
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

        $response = $this->postJson('/api/receivables', $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'due_date'      => [$message_due_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                ]);
    }

    public function testFailedWhenCreateReceivableWithExpenseCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $message = __('validation.exists', ['attribute' => 'category id']);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Receivable test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->postJson('/api/receivables', $data);

        $response->assertStatus(422)
            ->assertExactJson(['category_id' => [$message]]);

    }

    public function testCreateReceivableWithInstallmentsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $data = [
            'due_date'              => now()->format('Y-m-d'),
            'description'           => 'Receivable test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => 4,
            'installment_value'     => 25,
        ];

        $response = $this->postJson('/api/receivables', $data);

        $response->assertStatus(201)
            ->assertJsonCount(4, 'data');
    }

    public function testValidationErrorWhenCreateReceivableWithInstallments()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $data = [
            'due_date'              => now()->format('Y-m-d'),
            'description'           => 'Receivable test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => -2,
            'installment_value'     => -100,
        ];

        $message_installments_number    = __('validation.gt.numeric', ['attribute' => 'installments number', 'value' => 0]);
        $message_installments_value     = __('validation.gt.numeric', ['attribute' => 'installment value', 'value' => 0]);

        $response = $this->postJson('/api/receivables', $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'installment_value' => [$message_installments_value],
                'installments_number' => [$message_installments_number]
            ]);            
    }

    public function testGetReceivablesWithUnauthenticatedUser()
    {
        $response = $this->getJson('api/receivables');

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetReceivableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);
        
        AccountsScheduling::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->getJson('api/receivables');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testGetAReceivableByRangeDate()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $older_receivable = AccountsScheduling::factory()->create([
            'due_date'      => now()->format('Y-m-01'),
            'category_id'   => $category->id
        ]);

        $new_receivable = AccountsScheduling::factory()->create([
            'due_date'      => now()->format('Y-m-15'),
            'category_id'   => $category->id
        ]);

        $response = $this->getJson("/api/receivables?from={$new_receivable->due_date}&to={$new_receivable->due_date}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testGetNonExistentReceivable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $receivable = 'Invalid Receivables';

        $message = __('messages.account_scheduling.api_not_found');

        $response = $this->getJson("api/receivables/{$receivable}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetReceivableFromAnotherUser()
    {
        $testUser = User::factory()->create();

        $receivable = AccountsScheduling::withoutEvents(function () use ($testUser) {
            return AccountsScheduling::factory()->create([
                'user_id' => $testUser->id
            ]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/receivables/{$receivable->id}");

        $response->assertStatus(404);
    }

    public function testGetAReceivableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $receivable = AccountsScheduling::factory()->create(['category_id' => $category->id]);        

        $response = $this->getJson("/api/receivables/{$receivable->id}");

        $response->assertStatus(200)
                ->assertJsonPath('id', $receivable->id)
                ->assertJsonPath('name', $receivable->name);
    }

    public function testUpdateReceivableWithUnauthenticatedUser()
    {
        $receivable = 'receivable';

        $response = $this->putJson("/api/receivables/{$receivable}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentReceivable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $receivable = 'receivable';

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Update test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->putJson("/api/receivables/{$receivable}", $data);

        $response->assertStatus(404);
    }

    
    public function testValidationErrorWhenUpdateAReceivable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $receivable = AccountsScheduling::factory()->create();
        
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


        $response = $this->putJson("/api/receivables/{$receivable->id}", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'due_date' =>[$message_due_date],
                'description' =>[$message_description],
                'value' =>[$message_value],
                'category_id' =>[$message_category_id],
            ]);
    }

    public function testUpdateAReceivableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $receivable = AccountsScheduling::factory()->create(['category_id' => $category->id]);

        $data = [
            'due_date'      => now()->modify('+7 days')->format('Y-m-d'),
            'description'   => 'Receivable updated',
            'value'         => 200,
            'category_id'   => (Category::factory()->create(['type' => Category::INCOME]))->id
        ];

        $response = $this->putJson("/api/receivables/{$receivable->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('due_date', $data['due_date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value'])
            ->assertJsonPath('category.id', $data['category_id']);
    }

    public function testDeleteReceivableWithUnauthenticatedUser()
    {
        $receivable = 'receivable';

        $response = $this->putJson("/api/receivables/{$receivable}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testDeleteNonExistentReceivable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $receivable = 'receivable';

        $response = $this->deleteJson("/api/receivables/{$receivable}");

        $response->assertStatus(404);
    }

    public function testFailedWhenDeleteReceivableWithInvalidCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $receivables = AccountsScheduling::factory()->create(['category_id' => $category->id]);

        $message = __('messages.account_scheduling.not_receivable');

        $response = $this->deleteJson("/api/receivables/{$receivables->id}");

        $response->assertStatus(422)
        ->assertExactJson([
            'message' =>$message,
        ]);
    }

    public function testDeleteReceivableSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $receivables = AccountsScheduling::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/receivables/{$receivables->id}");

        $response->assertStatus(204);
    }

    public function testReceivementSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $receivables  = AccountsScheduling::factory()->create(['category_id' => $category->id]);

        $data = [
            'paid_date'     => now()->modify('+1 days')->format('Y-m-d'),
            'account_id'    => $account->id,
            'value'         => 100
        ];

        $response = $this->postJson("/api/receivables/{$receivables->id}/receivement", $data);

        $response->assertStatus(200)
            ->assertExactJson([
                'message' =>__('messages.account_scheduling.receivable_paid'),
            ]);
    }

    public function testValidationErrorWhenMakingReceivement()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $receivable  = AccountsScheduling::factory()->create(['category_id' => $category->id]);

        $message_paid_date  = __('validation.date_format', ['attribute' => 'paid date', 'format' => 'Y-m-d']);
        $message_account_id = __('validation.exists', ['attribute' => 'account id']);
        $message_value      = __('validation.filled', ['attribute' => 'value']);

        $data = [
            'paid_date'     => 'Invalid date',
            'account_id'    => 'Invalid account'
        ];

        $response = $this->postJson("/api/receivables/{$receivable}/receivement", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'paid_date'     => [$message_paid_date],
                'account_id'    => [$message_account_id],
                'value'         => [$message_value]
            ]);
    }

    public function testCancelReceivementSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();
        $category = Category::factory()->create(['type' => Category::INCOME]);

        $receivable = AccountsScheduling::factory()->create([
            'category_id'   => $category->id,
            'paid_date'     => now(),
            'paid'          => true
        ]);
        
        $entry = AccountEntry::factory()->create([
            'date'                  => $receivable->paid_date,
            'value'                 => $receivable->value,
            'account_id'            => $account->id,
            'category_id'           => $receivable->category_id,
            'account_scheduling_id' => $receivable->id
        ]);

        $response = $this->postJson("/api/receivables/{$receivable->id}/cancel-receivement");

        $response->assertStatus(200)
            ->assertExactJson([
                'message' =>__('messages.account_scheduling.receivable_cancel'),
            ]);
    }

    public function testFailedWhenCancelNonExistentReceivable()
    {
        Sanctum::actingAs(
            $this->user
        );

        $receivable = 'Receivable';

        $response = $this->postJson("/api/receivables/{$receivable}/cancel-receivement");

        $response->assertStatus(404);
    }

}
