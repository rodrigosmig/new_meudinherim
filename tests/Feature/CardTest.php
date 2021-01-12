<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function testCardsWithUnauthenticatedUser()
    {
        $response = $this->getJson('api/cards', []);

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }
}
