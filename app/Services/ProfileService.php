<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\ProfileRepositoryInterface;

class ProfileService
{
    protected $user;

    public function __construct(ProfileRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function updatePassword(array $data): bool
    {
        $user = auth()->user();
        
        return $this->repository->update($user, [
            'password' => Hash::make($data['password'])
        ]);
    }

    public function updateProfile(array $data): void
    {
        $data['enable_notification'] = isset($data['enable_notification']) && $data['enable_notification'] ? true : false;
        
        $user = auth()->user();

        if ($user->email !== $data['email']) {
            $data['email_verified_at'] = null;            
        }
        
        $this->repository->update($user, $data);
    }

    public function updateAvatar(array $data): void
    {        
        $user = auth()->user();

        if ($user->hasAvatar()) {
            Storage::delete($user->avatar);
        }

        $avatar = $data['file'];

        $avatarName = $user->id . '_avatar' . time() . '.' . $avatar->getClientOriginalExtension();

        $data['file']->storeAs('public/avatars', $avatarName);
        
        $user->avatar = "avatars/" . $avatarName;

        $this->repository->save($user);
    }

    /**
     * Returns users with notification enabled
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getUsersForNotification()
    {
        return $this->repository->getUsersForNotification();
    }

    public function getApiUser(): User
    {
        return auth()->user();
    }

    /**
     * Creates the default user account
     *
     * @return void
     */
    public function createDefaultUserAccount()
    {
        $user = auth()->user();

        $user->accounts()->create([
            'name'      => __('global.money'),
            'type'      => Account::MONEY,
        ]);
    }

    /**
     * Returns users with notification enabled
     *
     * @return User
     */
    public function createUser(array $data): User
    {
        $enable_notification = isset($data['enable_notification']) && $data['enable_notification'] == 'true' ? true : false;

        return $this->repository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'enable_notification' => $enable_notification
        ]);
    }

    /**
     * validates google recaptcha token
     *
     * @return bool
     */
    public function validateRecaptcha(string $token): bool
    {
        if (config('app.env') !== 'production') {
            return true;
        }

        $client = new Client();

        $response = $client->post(config('auth.google_recaptcha_url'),
            ['form_params'=>
                [
                    'secret'    => config('auth.google_recaptcha_secret'),
                    'response'  => $token
                 ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }
}
