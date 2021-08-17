<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;

class ProfileTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create(['password' => Hash::make('12345678')]);
    }
    
    public function testShowProfileWhenUnauthenticatedUser()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testGetUserSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonPath('id', $this->user->id)
            ->assertJsonPath('name', $this->user->name)
            ->assertJsonPath('email', $this->user->email);
    }

    public function testUpdateProfileWhenUnauthenticatedUser()
    {
        $response = $this->putJson('/api/users', []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testRequiredFieldsWhenUpdateAProfile()
    {
        Sanctum::actingAs(
            $this->user
        );
       
        $message_name = __('validation.required', ['attribute' => 'nome']);
        $message_email = __('validation.required', ['attribute' => 'e-mail']);

        $data = [
            'name' => '',
            'email' => ''
        ];

        $response = $this->putJson("/api/users/", $data);

        $response->assertStatus(422)
                ->assertJsonPath('name', [$message_name])
                ->assertJsonPath('email', [$message_email]);
    }

    public function testValidationErrorWhenUpdateAProfile()
    {
        Sanctum::actingAs(
            $this->user
        );

        $new_user = factory(User::class)->create();
        
        $message_name = __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 3]);
        $message_email = __('validation.unique', ['attribute' => 'e-mail']);

        $data = [
            'name' => 'T',
            'email' => $new_user->email
        ];

        $response = $this->putJson("/api/users/", $data);

        $response->assertStatus(422)
                ->assertJsonPath('name', [$message_name])
                ->assertJsonPath('email', [$message_email]);
    }

    public function testUpdateAProfileSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'name' => 'Test Updated',
            'email' => 'teste_updated@test.com'
        ];

        $response = $this->putJson("/api/users/", $data);

        $response->assertStatus(200)
            ->assertJsonPath('id', $this->user->id)
            ->assertJsonPath('name', $data['name'])
            ->assertJsonPath('email', $data['email']);
    }

    public function testUpdatePasswordWhenUnauthenticatedUser()
    {
        $response = $this->putJson('/api/users/password');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenUpdatePassword()
    {
        Sanctum::actingAs(
            $this->user
        );
       
        $message_password = __('messages.profile.incorrect_password');
        $message_password_min = __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]);
        $message_password_confirmation = __('validation.confirmed', ['attribute' => __('validation.attributes.password')]);

        $data = [
            'current_password' => '11111111111',
            'password' => '2342',
            'password_confirmation' => '43242'
        ];

        $response = $this->putJson("/api/users/password", $data);

        $response->assertStatus(422)
            ->assertJsonPath('errors.password', [$message_password_confirmation, $message_password_min])
            ->assertJsonPath('errors.current_password', [$message_password]);
    }

    public function testUpdatePasswordSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );
       
        $data = [
            'current_password' => '12345678',
            'password' => '987654321',
            'password_confirmation' => '987654321'
        ];

        $response = $this->putJson("/api/users/password", $data);

        $response->assertStatus(200)
            ->assertJsonPath('message', __('messages.profile.password_updated'));
    }
}
