<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountEntry;
use Laravel\Sanctum\Sanctum;
use App\Models\AccountBalance;
use Database\Factories\AccountBalanceFactory;

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

        $this->user = User::factory()->create();
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
            ->assertJsonPath('name', $data['name'])
            ->assertJsonPath('type.id', $data['type']);
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
        
        Account::factory()->count(3)->create([
            'active'    => false,
            'user_id'   => $this->user->id
        ]);

        Account::factory()->count(2)->create([
            'active'    => true,
            'user_id'   => $this->user->id
        ]);

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
        $testUser = User::factory()->create();

        $account = Account::withoutEvents(function () use ($testUser) {
            return Account::factory()->create(['user_id' => $testUser->id]);
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

        $account = Account::factory()->create();

        $response = $this->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(200)
                ->assertJsonPath('id', $account->id)
                ->assertJsonPath('name', $account->name);
    }

    public function testUpdateAccountWithUnauthenticatedUser()
    {
        $account = 'account';

        $response = $this->putJson("/api/accounts/{$account}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentAccount()
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

        $account = Account::factory()->create();
        
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

        $account = Account::factory()->create(['type' => Account::CHECKING_ACCOUNT]);

        $data = [
            'name' => 'Account Test',
            'type' => Account::INVESTMENT,
        ];

        $response = $this->putJson("/api/accounts/{$account->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('name', $data['name'])
            ->assertJsonPath('type.id', $data['type']);
    }

    public function testDeleteAccountWithUnauthenticatedUser()
    {
        $account = 'account';

        $response = $this->putJson("/api/accounts/{$account}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
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

        $account = Account::factory()->create();
        
        AccountEntry::factory()->create(['account_id' => $account->id]);

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

        $account = Account::factory()->create();

        $response = $this->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(204);
    }

    public function testGetAccountBalanceWhenUnauthenticatedUser() 
    {
        $response = $this->getJson('/api/accounts/balance/1');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetAccountBalanceWithInvalidAccount() 
    {
        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/accounts/balance/invalid_account');

        $message = __('messages.accounts.api_not_found');

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetAccountBalanceAllAccounts() 
    {
        Sanctum::actingAs(
            $this->user
        );

        $account_balance1 = AccountBalance::factory()->create();

        $account_balance2 = AccountBalance::factory()->create();

        $total = $account_balance1->current_balance + $account_balance2->current_balance;

        $response = $this->getJson('/api/accounts/balance/all');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'balances')
            ->assertJsonPath('total', $total);
    }

    public function testGetBalanceFromAnAccount() 
    {
        Sanctum::actingAs(
            $this->user
        );

        $account_balance = AccountBalance::factory()->create();

        $others_balances = AccountBalance::factory()->count(3)->create();

        $response = $this->getJson("/api/accounts/balance/" . $account_balance->account->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'balances')
            ->assertJsonStructure([
                'balances' =>[
                    [
                        'account_id',
                        'account_name',
                        'balance',
                    ]
                ],
            ]);
    }

    public function testGetInactiveAccounts()
    {
        Sanctum::actingAs(
            $this->user
        );

        Account::factory()->count(3)->create([
            'active'    => false,
            'user_id'   => $this->user->id
        ]);

        Account::factory()->count(2)->create([
            'active'    => true,
            'user_id'   => $this->user->id
        ]);

        $response = $this->getJson("/api/accounts?active=false");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
