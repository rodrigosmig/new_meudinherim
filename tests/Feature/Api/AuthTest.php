<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function testInvalidEmailWhenCreateUser()
    {       
        $data = [
            'name'      => 'Test Name',
            'email'     => "invalid email",
            'password'  => '12345678',
            'password_confirmation'  => '12345678'
        ];

        $message = __('validation.email', ['attribute' => 'e-mail']);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['email' =>[$message]]);
    }

    public function testInvalidPasswordConfirmationWhenCreateUser()
    {       
        $data = [
            'name'      => 'Test Name',
            'email'     => "test@gmail.com",
            'password'  => '12345678',
            'password_confirmation'  => '1234567'
        ];

        $message = __('validation.confirmed', ['attribute' =>  __('validation.attributes.password')]);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['password' =>[$message]]);
    }

    public function testEmailAlreadyInUse()
    {
        $user = factory(User::class)->create();

        $data = [
            'name'      => 'Test Name',
            'email'     => $user->email,
            'password'  => '12345678',
            'password_confirmation'  => '12345678'
        ];

        $message = __('validation.unique', ['attribute' => 'e-mail']);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['email' =>[$message]]);
    }

    public function testCreateUserSuccessfully()
    {       
        $data = [
            'name'      => 'Test Name',
            'email'     => "test@gmail.com",
            'password'  => '12345678',
            'password_confirmation'  => '12345678'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
                ->assertJsonPath('name', $data['name'])
                ->assertJsonPath('email', $data['email']);
    }

    public function testLoginFailedWithInvalidEmail()
    {
        $data = [
            'email'     => 'test@test.com',
            'password'  => '12345678',
            'device'    => 'Test'
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(404);
    }

    public function testLoginFailedWithoutDeviceField()
    {
        $data = [
            'email'     => 'test@test.com',
            'password'  => '12345678',
        ];

        $message = __('validation.filled', ['attribute' =>  'device']);

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(422)
                ->assertExactJson(['device' =>[$message]]);
    }

    public function testLoginSuccesfully()
    {
        $user = factory(User::class)->create(['password' => Hash::make('12345678')]);

        $data = [
            'email'     => $user->email,
            'password'  => '12345678',
            'device'    => 'Test'
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
