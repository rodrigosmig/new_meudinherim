<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceEntry;
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
            ->assertJsonPath('name', $data['name'])
            ->assertJsonPath('pay_day', $data['pay_day'])
            ->assertJsonPath('closing_day', $data['closing_day'])
            ->assertJsonPath('credit_limit', $data['credit_limit']);
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
                ->assertJsonPath('id', $card->id)
                ->assertJsonPath('name', $card->name);
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
                ->assertJsonPath('name', $data['name'])
                ->assertJsonPath('pay_day', $data['pay_day'])
                ->assertJsonPath('closing_day', $data['closing_day'])
                ->assertJsonPath('credit_limit', $data['credit_limit']);
    }

    public function testDeleteCardWithUnauthenticatedUser()
    {
        $card = 'card';

        $response = $this->putJson("/api/cards/{$card}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
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

    public function testDeleteACardAssociatedWithInvoicesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();
        
        factory(Invoice::class)->create(['card_id' => $card->id]);

        $message = __('messages.cards.not_delete');

        $response = $this->deleteJson("/api/cards/{$card->id}");

        $response->assertStatus(204);
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

    public function testGetInvoicesWithUnauthenticatedUser()
    {
        $response = $this->getJson("/api/cards/1/invoices");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetOpenInvoicesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();
        $card = $invoice->card;

        $new_invoice = factory(Invoice::class)->create([
            'due_date' => now()->modify("+1 month")->format('Y-m-d'),
            'card_id' => $card->id,
        ]);

        $response = $this->getJson("/api/cards/{$card->id}/invoices");

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    public function testGetPaidInvoicesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();
        $card = $invoice->card;

        $new_invoice = factory(Invoice::class)->create([
            'due_date' => now()->modify("+1 month")->format('Y-m-d'),
            'card_id' => $card->id,
        ]);

        $paid_invoice = factory(Invoice::class)->create([
            'due_date'  => now()->modify("+1 month")->format('Y-m-d'),
            'card_id'   => $card->id,
            'paid'      => true
        ]);

        $response = $this->getJson("/api/cards/{$card->id}/invoices?status=paid");

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    public function testGetAInvoiceWithUnauthenticatedUser()
    {
        $response = $this->getJson("/api/cards/1/invoices/1");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetAInvalidInvoice()
    {
        Sanctum::actingAs(
            $this->user
        );

        $message = __('messages.invoices.api_not_found');

        $invoice = factory(Invoice::class)->create();
        $card = $invoice->card;

        $response = $this->getJson("/api/cards/{$card->id}/invoices/test");

        $response->assertStatus(404);
    }

    public function testGetAInvoiceSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();
        $card = $invoice->card;

        $response = $this->getJson("/api/cards/{$card->id}/invoices/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $invoice->id)
            ->assertJsonPath('due_date', $invoice->due_date)
            ->assertJsonPath('closing_date', $invoice->closing_date)
            ->assertJsonPath('amount', $invoice->amount)
            ->assertJsonPath('paid', false);
    }

    public function testGetOpenInvoicesWithUnauthenticatedUser()
    {
        $response = $this->getJson("/api/cards/invoices/open");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetLastOpenInvoicesFromAllCards()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();
        $card = $invoice->card;

        $another_invoice_another_card = factory(Invoice::class)->create([
            'amount' => 125.55
        ]);

        $total = $invoice->amount + $another_invoice_another_card->amount;

        $response = $this->getJson("/api/cards/invoices/open");
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'invoices')
            ->assertJsonPath('total', $total);
    }
}
