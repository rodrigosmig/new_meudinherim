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

        $this->user = factory(User::class)->create();
    }

    public function testCreateAccountEntryWhenUnauthenticatedUser()
    {
        $account = 'Account';

        $response = $this->postJson("/api/accounts/{$account}/entries");

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
            'date'                  => 'Invalid date',
            'description'           => 'De',
            'value'                 => -100,
            'category_id'           => 'Invalid category',
        ];        

        $message_date           = __('validation.date_format', ['attribute' => __('validation.attributes.date'), 'format' => 'Y-m-d']);
        $message_description    = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value          = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_category_id    = __('validation.exists', ['attribute' => 'category id']);

        $response = $this->postJson("/api/accounts/{$account}/entries", $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'date'          => [$message_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                ]);
    }

    public function testFailedWhenCreateAccountEntryWithInvalidAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $account = 'Invalid Account';

        $data = [
            'date'                  => now()->format('Y-m-d'),
            'description'           => 'Account Entry test',
            'value'                 => 100,
            'category_id'           => $category->id
        ];

        $response = $this->postJson("/api/accounts/{$account}/entries", $data);

        $response->assertStatus(404)
            ->assertExactJson(['message' => __('messages.accounts.api_not_found')]);
    }

    public function testCreateAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $account = factory(Account::class)->create();

        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Account Entry test',
            'value'         => 100,
            'category_id'   => $category->id,
        ];

        $response = $this->postJson("/api/accounts/{$account->id}/entries", $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.date', $data['date'])
            ->assertJsonPath('data.description', $data['description'])
            ->assertJsonPath('data.value', $data['value']);
    }

    public function testGetAccountEntryWithUnauthenticatedUser()
    {
        $account = 'Invalid Account';

        $response = $this->postJson("/api/accounts/{$account}/entries");

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
        $testUser = factory(User::class)->create();

        $entry = AccountEntry::withoutEvents(function () use ($testUser) {
            return factory(AccountEntry::class)->create([
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

        $account = factory(Account::class)->create();

        $entry = factory(AccountEntry::class, 3)->create(['account_id' => $account->id]);
        
        $response = $this->getJson("/api/accounts/{$account->id}/entries");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testGetAPayableByRangeDate()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category   = factory(Category::class)->create(['type' => Category::EXPENSE]);
        $account    = factory(Account::class)->create();

        $older_entry = factory(AccountEntry::class)->create([
            'date'          => now()->format('Y-m-01'),
            'account_id'    => $account->id, 
            'category_id'   => $category->id
        ]);

        $new_entry = factory(AccountEntry::class)->create([
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

        $account = factory(Account::class)->create();

        $entry = factory(AccountEntry::class)->create(['account_id' => $account->id]);
        
        $response = $this->getJson("/api/account-entries/{$entry->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $entry->id)
            ->assertJsonPath('data.date', $entry->date)
            ->assertJsonPath('data.value', $entry->value);
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

        $account = factory(Account::class)->create();

        $entry = factory(AccountEntry::class)->create(['account_id' => $account->id]);

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

        $response = $this->putJson("/api/account-entries/{$entry->id}", $data);


        $response->assertStatus(422)
                ->assertExactJson([
                    'date'          => [$message_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                ]);
    }

    public function testUpdateNonExistentAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'entry';

        $category = factory(Category::class)->create(['type' => Category::INCOME]);

        $data = [
            'date'      => now()->format('Y-m-d'),
            'description'   => 'Update test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->putJson("/api/account-entries/{$entry}", $data);

        $response->assertStatus(404);
    }

    public function testUpdateAccountEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::INCOME]);

        $account = factory(Account::class)->create();

        $entry = factory(AccountEntry::class)->create(['account_id' => $account->id]);

        $data = [
            'date'          => now()->modify('+3 days')->format('Y-m-d'),
            'description'   => 'Account Entry updated',
            'value'         => 100,
            'category_id'   => $category->id,
        ];

        $response = $this->putJson("/api/account-entries/{$entry->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.date', $data['date'])
            ->assertJsonPath('data.description', $data['description'])
            ->assertJsonPath('data.value', $data['value']);
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

        $account = factory(Account::class)->create();

        $entry = factory(AccountEntry::class)->create(['account_id' => $account->id]);

        $response = $this->deleteJson("/api/account-entries/{$entry->id}");

        $response->assertStatus(204);
    }
}
