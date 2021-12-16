<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Parcel;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\InvoiceEntry;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class InvoiceEntryTest extends TestCase
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

    public function testCreateInvoiceEntryWhenUnauthenticatedUser()
    {
        $card = 'Card';

        $response = $this->postJson("/api/cards/{$card}/entries");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingAInvoiceEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = "Invalid card";
        
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

        $response = $this->postJson("/api/cards/{$card}/entries", $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'date'          => [$message_date],
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                ]);
    }

    public function testFailedWhenCreateInvoiceWithInvalidCard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = 'Invalid Card';

        $data = [
            'date'                  => now()->format('Y-m-d'),
            'description'           => 'Invoice Entry test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => 3,
            'installment_value'     => 50,
        ];

        $response = $this->postJson("/api/cards/{$card}/entries", $data);

        $response->assertStatus(404)
            ->assertExactJson(['message' => __('messages.entries.invalid_card')]);
    }

    public function testValidationErrorWhenCreateInvoiceEntryWithInstallments()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = factory(Card::class)->create();

        $data = [
            'date'                  => now()->format('Y-m-d'),
            'description'           => 'Invoice Entry test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => -2,
            'installment_value'     => -100,
        ];

        $message_installments_number    = __('validation.gt.numeric', ['attribute' => 'installments number', 'value' => 0]);
        $message_installments_value     = __('validation.gt.numeric', ['attribute' => 'installment value', 'value' => 0]);

        $response = $this->postJson("/api/cards/{$card}/entries", $data);

        $response->assertStatus(422)
            ->assertExactJson([
                'installment_value' => [$message_installments_value],
                'installments_number' => [$message_installments_number]
            ]);            
    }

    public function testCreateInvoiceEntryWithInstallmentsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = factory(Card::class)->create();

        $data = [
            'date'                  => now()->format('Y-m-d'),
            'description'           => 'Invoice Entry test',
            'value'                 => 100,
            'category_id'           => $category->id,
            'installment'           => 'on',
            'installments_number'   => 3,
            'installment_value'     => 50,
        ];

        $response = $this->postJson("/api/cards/{$card->id}/entries", $data);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testCreateInvoiceEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = factory(Card::class)->create();

        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Invoice Entry test',
            'value'         => 100,
            'category_id'   => $category->id,
        ];

        $response = $this->postJson("/api/cards/{$card->id}/entries", $data);

        $response->assertStatus(201)
            ->assertJsonPath('date', $data['date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value']);
    }

    public function testGetInvoiceEntryWithUnauthenticatedUser()
    {
        $card = 'Invalid Card';

        $response = $this->postJson("/api/cards/{$card}/entries");

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testFailedWhenGetInvoiceEntryWithInvalidCard()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = 'Invalid Card';

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class, 3)->create(['invoice_id' => $invoice->id]);

        $response = $this->getJson("/api/cards/{$card}/invoices/{$invoice->id}/entries");

        $response->assertStatus(404)
            ->assertExactJson(['message' => __('messages.entries.invalid_card')]);
    }

    public function testFailedWhenGetInvoiceEntryWithInvalidInvoice()
    {
        Sanctum::actingAs(
            $this->user
        );

        $card = factory(Card::class)->create();

        $invoice = 'Invalid Invoice';

        $response = $this->getJson("/api/cards/{$card->id}/invoices/{$invoice}/entries");

        $response->assertStatus(404)
            ->assertExactJson(['message' => __('messages.entries.invalid_invoice')]);
    }

    public function testGetNonExistentAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'Invalid entry';

        $message = __('messages.entries.api_not_found');

        $response = $this->getJson("api/invoice-entries/{$entry}");

        $response->assertStatus(404)
            ->assertJsonPath('message', $message);
    }

    public function testGetInvoiceEntryFromAnotherUser()
    {
        $testUser = factory(User::class)->create();

        $entry = InvoiceEntry::withoutEvents(function () use ($testUser) {
            return factory(InvoiceEntry::class)->create([
                'user_id' => $testUser->id
            ]);
        });

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/invoice-entries/{$entry->id}");

        $response->assertStatus(404);
    }

    public function testGetEntriesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class, 3)->create(['invoice_id' => $invoice->id]);
        
        $response = $this->getJson("/api/cards/{$invoice->card->id}/invoices/{$invoice->id}/entries");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testGetInvoiceEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class)->create(['invoice_id' => $invoice->id]);
        
        $response = $this->getJson("/api/invoice-entries/{$entry->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('id', $entry->id)
            ->assertJsonPath('date', $entry->date)
            ->assertJsonPath('value', $entry->value);
    }

    public function testUpdateInvoiceEntryWithUnauthenticatedUser()
    {
        $entry = 'entry';

        $response = $this->putJson("/api/invoice-entries/{$entry}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenUpdateAInvoiceEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class)->create(['invoice_id' => $invoice->id]);

        $data = [
            'description'   => 'En',
            'value'         => -100,
            'category_id'   => 'Invalid',
        ];

        $response = $this->putJson("/api/invoice-entries/{$entry->id}");

        $message_description    = __('validation.min.string', ['attribute' => __('validation.attributes.description'), 'min' => 3]);
        $message_value          = __('validation.gt.numeric', ['attribute' => 'value', 'value' => 0]);
        $message_category_id    = __('validation.exists', ['attribute' => 'category id']);

        $response = $this->putJson("/api/invoice-entries/{$entry->id}", $data);

        $response->assertStatus(422)
                ->assertExactJson([
                    'description'   => [$message_description],
                    'value'         => [$message_value],
                    'category_id'   => [$message_category_id],
                ]);
    }

    public function testUpdateNonExistentInvoiceEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'entry';

        $category = factory(Category::class)->create(['type' => Category::INCOME]);

        $data = [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => 'Update test',
            'value'         => 100,
            'category_id'   => $category->id
        ];

        $response = $this->putJson("/api/invoice-entries/{$entry}", $data);

        $response->assertStatus(404);
    }

    public function testUpdateInvoiceEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class)->create(['invoice_id' => $invoice->id]);


        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Invoice Entry updated',
            'value'         => 100,
            'category_id'   => $category->id,
        ];

        $response = $this->putJson("/api/invoice-entries/{$entry->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('date', $data['date'])
            ->assertJsonPath('description', $data['description'])
            ->assertJsonPath('value', $data['value']);
    }

    public function testDeleteInvoiceEntryWithUnauthenticatedUser()
    {
        $entry = 'entry';

        $response = $this->deleteJson("/api/invoice-entries/{$entry}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testDeleteNonExistentInvoiceEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $entry = 'entry';

        $response = $this->deleteJson("/api/invoice-entries/{$entry}");

        $response->assertStatus(404);
    }

    public function testDeleteInvoiceEntrySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $invoice = factory(Invoice::class)->create();

        $entry = factory(InvoiceEntry::class)->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice-entries/{$entry->id}");

        $response->assertStatus(204);
    }

    public function testGetNextParcelsWhenUnauthenticatedUser()
    {
        $card = 'test';

        $entry = 'test';
        
        $response = $this->getJson("/api/invoice_entries/{$entry}/next-parcels");
        
        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetNextParcels()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = factory(Card::class)->create();

        $entry = factory(InvoiceEntry::class)->create([
            'category_id'   => $category->id,
            'has_parcels'   => true,
            'value'         => 300  
        ]);

        $invoice1 = factory(Invoice::class)->create([
            'card_id' => $card->id,
            'due_date'      => now()->modify('+30 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+24 days')->format('Y-m-d'),
        ]);

        $invoice2 = factory(Invoice::class)->create([
            'card_id' => $card->id,
            'due_date'      => now()->modify('+60 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+54 days')->format('Y-m-d'),
        ]);

        $invoice3 = factory(Invoice::class)->create([
            'card_id' => $card->id,
            'due_date'      => now()->modify('+90 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+84 days')->format('Y-m-d'),
        ]);

        $parcel1 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 1,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice1->id
        ]);

        $parcel2 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 2,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice2->id
        ]);

        $parcel3 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 3,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice3->id
        ]);

        $response = $this->getJson("/api/invoice_entries/{$entry->id}/next-parcels?parcel_number=1");
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testAnticipateParcelsSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = factory(Category::class)->create(['type' => Category::EXPENSE]);

        $card = factory(Card::class)->create();

        $entry = factory(InvoiceEntry::class)->create([
            'category_id'   => $category->id,
            'has_parcels'   => true,
            'value'         => 300  
        ]);

        $invoice1 = factory(Invoice::class)->create([
            'card_id'       => $card->id,
            'amount'        => 100,
            'due_date'      => now()->modify('+30 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+24 days')->format('Y-m-d'),
        ]);

        $invoice2 = factory(Invoice::class)->create([
            'card_id'       => $card->id,
            'amount'        => 100,
            'due_date'      => now()->modify('+60 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+54 days')->format('Y-m-d'),
        ]);

        $invoice3 = factory(Invoice::class)->create([
            'card_id'       => $card->id,
            'amount'        => 100,
            'due_date'      => now()->modify('+90 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+84 days')->format('Y-m-d'),
        ]);

        $this->assertEquals(100, $invoice3->amount);

        $parcel1 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 1,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice1->id
        ]);

        $parcel2 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 2,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice2->id
        ]);

        $parcel3 = factory(Parcel::class)->create([
            'parcelable_id' => $entry->id,
            'parcel_number' => 3,
            'parcel_total'  => 3,
            'value'         => 100,
            'category_id'   => $category->id,
            'invoice_id'    => $invoice3->id
        ]);
        
        $this->assertFalse($parcel3->anticipated);

        $parcels_ids = [
            'parcels' => [$parcel3->id]
        ];
        
        $response = $this->postJson("/api/invoice_entries/{$entry->id}/anticipate-parcels", $parcels_ids);
      
        $response->assertStatus(200)
        ->assertJsonPath('message', __('messages.parcels.anticipate'));

        $anticipated_parcel = app(ParcelRepositoryInterface::class)->findByid($parcel3->id);

        $this->assertTrue($anticipated_parcel->anticipated);

        $invoice3 = app(InvoiceRepositoryInterface::class)->findById($invoice3->id);
        
        $this->assertEquals(0, $invoice3->amount);

    }
}
