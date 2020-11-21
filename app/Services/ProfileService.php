<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function updatePassword(array $data): bool
    {
        $user = auth()->user();
        
        return $user->update([
            'password' => Hash::make($data['password'])
        ]);
    }

    public function updateProfile(array $data): bool
    {
        $data['enable_notification'] = isset($data['enable_notification']) ? true : false;
        
        $user = auth()->user();
        
        return $user->update($data);
    }

    public function updateAvatar(array $data): void
    {        
        $user = auth()->user();

        if ($user->hasAvatar()) {
            Storage::delete($user->avatar);
        }

        $avatar = Storage::put('public/avatar', $data['file']);
        
        $user->avatar = $avatar;
        $user->save();
    }

    /**
     * Returns users with notification enabled
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getUsersForNotification()
    {
        return $this->user
            ->where('enable_notification', true)
            ->get();
    }

    public function getApiUser()
    {
        return auth()->user();
    }

    /**
     * Returns users with notification enabled
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function createUser(array $data)
    {
        return $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
