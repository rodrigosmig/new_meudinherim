<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Account;
use App\Services\ProfileService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function testInvalidEmailWhenCreateUser()
    {       
        $data = [
            'name'                  => 'Test Name',
            'email'                 => "invalid email",
            'password'              => '12345678',
            'password_confirmation' => '12345678',
            'reCaptchaToken'        => 'test'
        ];

        $message = __('validation.email', ['attribute' => 'e-mail']);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['email' =>[$message]]);
    }

    public function testInvalidPasswordConfirmationWhenCreateUser()
    {       
        $data = [
            'name'                  => 'Test Name',
            'email'                 => "test@gmail.com",
            'password'              => '12345678',
            'password_confirmation' => '1234567',
            'reCaptchaToken'        => 'test'
        ];

        $message = __('validation.confirmed', ['attribute' =>  __('validation.attributes.password')]);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['password' =>[$message]]);
    }

    public function testEmailAlreadyInUse()
    {
        $user = User::factory()->create();

        $data = [
            'name'                  => 'Test Name',
            'email'                 => $user->email,
            'password'              => '12345678',
            'password_confirmation' => '12345678',
            'reCaptchaToken'        => 'test'
        ];

        $message = __('validation.unique', ['attribute' => 'e-mail']);

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
                ->assertExactJson(['email' =>[$message]]);
    }

    public function testCreateUserSuccessfully()
    {     
        $data = [
            'name'                  => 'Test Name',
            'email'                 => "test@gmail.com",
            'password'              => '12345678',
            'password_confirmation' => '12345678',
            'reCaptchaToken'        => 'test'
        ];

        $user = new User();
        $user->id = 1;
        $user->name = $data['name'];
        $user->email = $data['email'];
        
        $this->mock(ProfileService::class, function ($mock) use ($user) {
            $mock->shouldReceive('validateRecaptcha')->andReturn(true);
            $mock->shouldReceive('createUser')->andReturn($user);
            $mock->shouldReceive('createDefaultUserAccount');
        });

        $this->mock(CategoryService::class, function ($mock) use ($user) {
            $mock->shouldReceive('createDefaultCategories');
        });

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

        $response->assertStatus(422);
    }

    public function testLoginFailedWithoutDeviceField()
    {
        $data = [
            'email'     => 'test@test.com',
            'password'  => '12345678',
            'reCaptchaToken' => 'test'
        ];

        $message = __('validation.filled', ['attribute' =>  'device']);

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(422)
                ->assertExactJson(['device' =>[$message]]);
    }

    public function testLoginWithoutSendRecaptchaToken()
    {
        $data = [
            'email'     => 'test@test.com',
            'password'  => '12345678',
            'device'    => 'Test'
        ];

        $message = __('validation.filled', ['attribute' =>  're captcha token']);

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(422)
            ->assertExactJson(['reCaptchaToken' =>[$message]]);
    }

    public function testLoginWithInvalidRecaptchaToken()
    {
        $this->mock(ProfileService::class, function ($mock) {
            $mock->shouldReceive('validateRecaptcha')->andReturn(false);
        });

        $data = [
            'email'          => 'test@test.com',
            'password'       => '12345678',
            'device'         => 'Test',
            'reCaptchaToken' => 'test'
        ];

        $message = __('messages.recaptcha.invalid_token');

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(422)
            ->assertExactJson(['error' => $message]);
    }

    public function testLoginSuccesfully()
    {
        $user = User::factory()->create(['password' => Hash::make('12345678')]);

        $this->mock(ProfileService::class, function ($mock) use ($user) {
            $mock->shouldReceive('validateRecaptcha')->andReturn(true);
            $mock->shouldReceive('findByEmail')->andReturn($user);
        });

        $data = [
            'email'     => $user->email,
            'password'  => '12345678',
            'device'    => 'Test',
            'reCaptchaToken' => 'test'
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['token'])
            ->assertJsonStructure(['user'])
            ->assertJsonPath('user.email', $user->email);
    }
}
