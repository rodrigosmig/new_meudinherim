<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountEntry;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
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
    
    public function testCreateAccountWhenUnauthenticatedUser()
    {
        $response = $this->postJson('/api/accounts', []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingAAccount()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $data = [
            'name' => 'T',
            'type' => 'Type Test',
        ];

        $message_name = __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 3]);
        $message_type = __('validation.in', ['attribute' => 'type']);


        $response = $this->postJson('/api/accounts', $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'name' =>[$message_name],
                    'type' =>[$message_type],                 
                ]);
    }

    public function testCreateAccountSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'name' => 'Account Test',
            'type' => Account::CHECKING_ACCOUNT,
        ];

        $response = $this->postJson('/api/accounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.type', $data['type']);
    }

    public function testGetAccountsWithUnauthenticatedUser()
    {
        $response = $this->getJson('api/accounts');

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetAccountsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        factory(Account::class, 2)->create();

        $response = $this->getJson('api/accounts');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testGetNonExistentAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = 'Invalid Account';

        $message = __('messages.accounts.api_not_found');

        $response = $this->getJson("api/accounts/{$account}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetAccountFromAnotherUser()
    {
        $testUser = factory(User::class)->create();

        $account = Account::withoutEvents(function () use ($testUser) {
            return factory(Account::class)->create(['user_id' => $testUser->id]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(404);
    }

    public function testGetAAccountSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();

        $response = $this->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(200)
                ->assertJsonPath('data.id', $account->id)
                ->assertJsonPath('data.name', $account->name);
    }

    public function testUpdateAccountWithUnauthenticatedUser()
    {
        $account = 'account';

        $response = $this->putJson("/api/accounts/{$account}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentaccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = 'account';

        $data = [
            'name' => 'Account Test',
            'type' => Account::CHECKING_ACCOUNT,
        ];

        $response = $this->putJson("/api/accounts/{$account}", $data);

        $response->assertStatus(404);
    }

    public function testValidationErrorWhenUpdateAAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();
        
        $message_name = __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 3]);
        $message_type = __('validation.in', ['attribute' => 'type']);

        $data = [
            'name' => 'T',
            'type' => 'test',
        ];

        $response = $this->putJson("/api/accounts/{$account->id}", $data);

        $response->assertStatus(422)
                ->assertJsonPath('name', [$message_name])
                ->assertJsonPath('type', [$message_type]);
    }

    public function testUpdateAAccountSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create(['type' => Account::CHECKING_ACCOUNT]);

        $data = [
            'name' => 'Account Test',
            'type' => Account::INVESTMENT,
        ];

        $response = $this->putJson("/api/accounts/{$account->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.type', $data['type']);
    }

    public function testDeleteAccountWithUnauthenticatedUser()
    {
        $account = 'account';

        $response = $this->putJson("/api/accounts/{$account}");

        $response->assertStatus(401);
    }

    public function testDeleteNonExistentAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = 'account';

        $response = $this->deleteJson("/api/accounts/{$account}");

        $response->assertStatus(404);
    }

    public function testFailedToDeleteAAccountAssociatedWithAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();
        
        factory(AccountEntry::class)->create(['account_id' => $account->id]);

        $message = __('messages.accounts.not_delete');

        $response = $this->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(400)
                ->assertJsonPath('message', $message);
    }

    public function testDeleteAccountSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account = factory(Account::class)->create();

        $response = $this->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(204);
    }

}
