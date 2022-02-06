<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\AccountEntry;
use Laravel\Sanctum\Sanctum;

class AccountEntryTest extends TestCase
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

    public function testCreateAccountEntryWhenUnauthenticatedUser()
    {
        $account = 'Account';

        $response = $this->postJson("/api/account-entries");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingAAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = "Invalid account";
        
        $data = [
            'date'          => 'Invalid date',
            'description'   => 'De',
            'value'         => -100,
            'category_id'   => 'Invalid category',
        ];        

        $message_date           = __('validation.date_format', ['attribute' => __('validation.attributes.date'), 'format' => 'Y-m-d']);
        $message_description    = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value          = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_category_id    = __('validation.exists', ['attribute' => 'category id']);
        $message_account_id     = __('validation.filled', ['attribute' => 'account id']);

        $response = $this->postJson("/api/account-entries", $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'date'          => [$message_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                    'account_id'    => [$message_account_id],
                ]);
    }

    public function testFailedWhenCreateAccountEntryWithInvalidAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Account Entry test',
            'value'         => 100,
            'category_id'   => $category->id,
            'account_id'    => 'Invalid Account'
        ];

        $response = $this->postJson("/api/account-entries", $data);

        $response->assertStatus(422)
            ->assertExactJson(['account_id' => [__('validation.exists', ['attribute' => 'account id'])]]);
    }

    public function testCreateAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $account = Account::factory()->create();

        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Account Entry test',
            'value'         => 100,
            'category_id'   => $category->id,
            'account_id'      => $account->id
        ];

        $response = $this->postJson("/api/account-entries", $data);

        $response->assertStatus(201)
            ->assertJsonPath('date', $data['date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value']);
    }

    public function testGetAccountEntryWithUnauthenticatedUser()
    {
        $account = 'Invalid Account';

        $response = $this->getJson("/api/accounts/{$account}/entries");

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testFailedWhenGetAccountEntryWithInvalidAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = 'Invalid Account';

        $response = $this->getJson("/api/accounts/{$account}/entries");

        $response->assertStatus(404)
            ->assertExactJson(['message' => __('messages.accounts.api_not_found')]);
    }

    public function testGetNonExistentAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'Invalid entry';

        $message = __('messages.entries.api_not_found');

        $response = $this->getJson("api/account-entries/{$entry}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetAccountEntryFromAnotherUser()
    {
        $testUser = User::factory()->create();

        $entry = AccountEntry::withoutEvents(function () use ($testUser) {
            return AccountEntry::factory()->create([
                'user_id' => $testUser->id
            ]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/account-entries/{$entry->id}");

        $response->assertStatus(404);
    }

    public function testGetEntriesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();

        $entry = AccountEntry::factory()->count(3)->create(['account_id' => $account->id]);
        
        $response = $this->getJson("/api/accounts/{$account->id}/entries");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testGetAPayableByRangeDate()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category   = Category::factory()->create(['type' => Category::EXPENSE]);
        $account    = Account::factory()->create();

        $older_entry = AccountEntry::factory()->create([
            'date'          => now()->format('Y-m-01'),
            'account_id'    => $account->id, 
            'category_id'   => $category->id
        ]);

        $new_entry = AccountEntry::factory()->create([
            'date'          => now()->format('Y-m-15'),
            'account_id'    => $account->id,
            'category_id'   => $category->id
        ]);

        $response = $this->getJson("/api/accounts/{$account->id}/entries?from={$new_entry->date}&to={$new_entry->date}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

    }

    public function testGetAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();

        $entry = AccountEntry::factory()->create(['account_id' => $account->id]);
        
        $response = $this->getJson("/api/account-entries/{$entry->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('id', $entry->id)
            ->assertJsonPath('date', $entry->date)
            ->assertJsonPath('value', $entry->value);
    }

    public function testUpdateAccountEntryWithUnauthenticatedUser()
    {
        $entry = 'entry';

        $response = $this->putJson("/api/account-entries/{$entry}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenUpdateAAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();

        $entry = AccountEntry::factory()->create(['account_id' => $account->id]);

        $data = [
            'date'          => 'Invalid date',
            'description'   => 'De',
            'value'         => -100,
            'category_id'   => 'Invalid category',
        ];        

        $message_date           = __('validation.date_format', ['attribute' => __('validation.attributes.date'), 'format' => 'Y-m-d']);
        $message_description    = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value          = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_category_id    = __('validation.exists', ['attribute' => 'category id']);
        $message_account_id     = __('validation.filled', ['attribute' => 'account id']);

        $response = $this->putJson("/api/account-entries/{$entry->id}", $data);


        $response->assertStatus(422)
                ->assertExactJson([
                    'date'          => [$message_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                    'account_id'    => [$message_account_id]
                ]);
    }

    public function testUpdateNonExistentAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'entry';

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $account = Account::factory()->create();

        $data = [
            'date'      => now()->format('Y-m-d'),
            'description'   => 'Update test',
            'value'         => 100,
            'category_id'   => $category->id,
            'account_id'    => $account->id
        ];

        $response = $this->putJson("/api/account-entries/{$entry}", $data);

        $response->assertStatus(404);
    }

    public function testUpdateAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $account = Account::factory()->create();

        $entry = AccountEntry::factory()->create(['account_id' => $account->id]);

        $data = [
            'date'          => now()->modify('+3 days')->format('Y-m-d'),
            'description'   => 'Account Entry updated',
            'value'         => 100,
            'category_id'   => $category->id,
            'account_id'    => $account->id
        ];

        $response = $this->putJson("/api/account-entries/{$entry->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('date', $data['date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value']);
    }

    public function testDeleteAccountEntryWithUnauthenticatedUser()
    {
        $entry = 'entry';

        $response = $this->deleteJson("/api/account-entries/{$entry}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testDeleteNonExistentAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'entry';

        $response = $this->deleteJson("/api/account-entries/{$entry}");

        $response->assertStatus(404);
    }

    public function testDeleteAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = Account::factory()->create();

        $entry = AccountEntry::factory()->create(['account_id' => $account->id]);

        $response = $this->deleteJson("/api/account-entries/{$entry->id}");

        $response->assertStatus(204);
    }

    public function testAccountTransferWithUnauthenticatedUser()
    {
        $response = $this->postJson("/api/account-entries/account-transfer");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenMakingAAccountTransfer()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            "description"               => "d",
            "date"                      => "20210509",
            "value"                     => "Test",
            "source_account_id"         => "9999",
            "destination_account_id"    => "9999",
            "source_category_id"        => "9999",
            "destination_category_id"   => "9999",
        ];
        
        $message_date                       = __('validation.date_format', ['attribute' => __('validation.attributes.date'), 'format' => 'Y-m-d']);
        $message_description                = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value_gt_zero              = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_value_numeric              = __('validation.numeric', ['attribute' => 'value']);
        $message_source_category_id         = __('validation.exists', ['attribute' => 'source category id']);
        $message_destination_category_id    = __('validation.exists', ['attribute' => 'destination category id']);

        $response = $this->postJson("/api/account-entries/account-transfer", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'description'               => [$message_description],
                'date'                      => [$message_date],
                'value'                     => [$message_value_numeric, $message_value_gt_zero],
                'source_category_id'        => [$message_source_category_id],
                'destination_category_id'   => [$message_destination_category_id],
            ]);

    }

    public function testErrorWhenMakingAAccountTransferToTheSameAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $source_account = Account::factory()->create();

        $source_category        = Category::factory()->create(['type' => Category::EXPENSE]);
        $destination_category   = Category::factory()->create(['type' => Category::INCOME]);

        $data = [
            "description"               => "Test description",
            "date"                      => now()->format('Y-m-d'),
            "value"                     => 100,
            "source_account_id"         => $source_account->id,
            "destination_account_id"    => $source_account->id,
            "source_category_id"        => $source_category->id,
            "destination_category_id"   => $destination_category->id,
        ];

        $response = $this->postJson("/api/account-entries/account-transfer", $data);

        $response->assertStatus(400)
            ->assertJsonPath('message', __('messages.accounts.equal_accounts'));

    }

    public function testErrorWhenMakingAAccountTransferWithInvalidCategories()
    {
        Sanctum::actingAs(
            $this->user
        );

        $source_account = Account::factory()->create();

        $source_category = Category::factory()->create(['type' => Category::INCOME]);
        $destination_category = Category::factory()->create(['type' => Category::EXPENSE]);

        $data = [
            "description" => "Test description",
            "date" => now()->format('Y-m-d'),
            "value" => 100,
            "source_account_id" => $source_account->id,
            "destination_account_id" => $source_account->id,
            "source_category_id" => $source_category->id,
            "destination_category_id" => $destination_category->id,
        ];

        $message_source_category_id         = __('validation.exists', ['attribute' => 'source category id']);
        $message_destination_category_id    = __('validation.exists', ['attribute' => 'destination category id']);

        $response = $this->postJson("/api/account-entries/account-transfer", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'source_category_id'        => [$message_source_category_id],
                'destination_category_id'   => [$message_destination_category_id],
            ]);

    }

    public function testAccountTransferSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $source_account         = Account::factory()->create();
        $destination_account    = Account::factory()->create();

        $source_category = Category::factory()->create(['type' => Category::EXPENSE]);
        $destination_category = Category::factory()->create(['type' => Category::INCOME]);

        $data = [
            "description" => "Test description",
            "date" => now()->format('Y-m-d'),
            "value" => 100,
            "source_account_id" => $source_account->id,
            "destination_account_id" => $destination_account->id,
            "source_category_id" => $source_category->id,
            "destination_category_id" => $destination_category->id,
        ];

        $response = $this->postJson("/api/account-entries/account-transfer", $data);

        $response->assertStatus(200)
            ->assertJsonPath('message', __('messages.accounts.transfer_completed'));
    }

}
