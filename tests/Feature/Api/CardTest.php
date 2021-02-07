<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Invoice;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;

class CardTest extends TestCase
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

    public function testCreateCardWhenUnauthenticatedUser()
    {
        $response = $this->postJson('/api/cards', []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingACard()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $data = [
            'name'          => 'Card Test',
            'pay_day'       => '-1',
            'closing_day'   => '35',
            'credit_limit'  => 'Fake limit'
        ];

        $message_pay_day        = __('validation.min.numeric', ['attribute' => 'pay day', 'min' => 1]);
        $message_closing_day    = __('validation.max.numeric', ['attribute' => 'closing day', 'max' => 31]);
        $message_credit_limit   = __('validation.numeric', ['attribute' => 'credit limit']);

        $response = $this->postJson('/api/cards', $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'pay_day'       =>[$message_pay_day],
                    'credit_limit'   =>[$message_credit_limit],
                    'closing_day'   =>[$message_closing_day]                    
                ]);
    }

    public function testCreateCardSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'name'          => 'Card Test',
            'pay_day'       => '10',
            'closing_day'   => '3',
            'credit_limit'  => 5000
        ];

        $response = $this->postJson('/api/cards', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.pay_day', $data['pay_day'])
            ->assertJsonPath('data.closing_day', $data['closing_day'])
            ->assertJsonPath('data.credit_limit', $data['credit_limit']);
    }

    public function testGetCardsWithUnauthenticatedUser()
    {
        $response = $this->getJson('api/cards');

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetCardsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        factory(Card::class, 2)->create();

        $response = $this->getJson('api/cards');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testGetNonExistentCard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = 'Invalid Card';

        $message = __('messages.cards.api_not_found');

        $response = $this->getJson("api/cards/{$card}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetCardFromAnotherUser()
    {
        $testUser = factory(User::class)->create();

        $card = Card::withoutEvents(function () use ($testUser) {
            return factory(Card::class)->create(['user_id' => $testUser->id]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/cards/{$card->id}");

        $response->assertStatus(404);
    }

    public function testGetACardSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();

        $response = $this->getJson("/api/cards/{$card->id}");

        $response->assertStatus(200)
                ->assertJsonPath('data.id', $card->id)
                ->assertJsonPath('data.name', $card->name);
    }

    public function testUpdateCardWithUnauthenticatedUser()
    {
        $card = 'card';

        $response = $this->putJson("/api/cards/{$card}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentCard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = 'card';

        $data = [
            'name'          => 'Card Test',
            'pay_day'       => '10',
            'closing_day'   => '3',
            'credit_limit'  => 5000
        ];

        $response = $this->putJson("/api/cards/{$card}", $data);

        $response->assertStatus(404);
    }

    public function testValidationErrorWhenUpdateACard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();
        
        $message_pay_day        = __('validation.min.numeric', ['attribute' => 'pay day', 'min' => 1]);
        $message_closing_day    = __('validation.max.numeric', ['attribute' => 'closing day', 'max' => 31]);
        $message_credit_limit   = __('validation.numeric', ['attribute' => 'credit limit']);

        $data = [
            'name'          => 'Card Test',
            'pay_day'       => '-1',
            'closing_day'   => '35',
            'credit_limit'  => 'Fake limit'
        ];

        $response = $this->putJson("/api/cards/{$card->id}", $data);

        $response->assertStatus(422)
                ->assertJsonPath('pay_day', [$message_pay_day])
                ->assertJsonPath('closing_day', [$message_closing_day])
                ->assertJsonPath('credit_limit', [$message_credit_limit]);
    }

    public function testUpdateACardSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();

        $data = [
            'name'          => 'Card Updated',
            'pay_day'       => '15',
            'closing_day'   => '5',
            'credit_limit'  => 8000
        ];

        $response = $this->putJson("/api/cards/{$card->id}", $data);

        $response->assertStatus(200)
                ->assertJsonPath('data.name', $data['name'])
                ->assertJsonPath('data.pay_day', $data['pay_day'])
                ->assertJsonPath('data.closing_day', $data['closing_day'])
                ->assertJsonPath('data.credit_limit', $data['credit_limit']);
    }

    public function testDeleteCardWithUnauthenticatedUser()
    {
        $card = 'card';

        $response = $this->putJson("/api/cards/{$card}");

        $response->assertStatus(401);
    }

    public function testDeleteNonExistentCard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = 'card';

        $response = $this->deleteJson("/api/cards/{$card}");

        $response->assertStatus(404);
    }

    public function testFailedToDeleteACardAssociatedWithInvoices()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();
        
        factory(Invoice::class)->create(['card_id' => $card->id]);

        $message = __('messages.cards.not_delete');

        $response = $this->deleteJson("/api/cards/{$card->id}");

        $response->assertStatus(400)
                ->assertJsonPath('message', $message);
    }

    public function testDeleteCardSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();

        $response = $this->deleteJson("/api/cards/{$card->id}");

        $response->assertStatus(204);
    }
}
