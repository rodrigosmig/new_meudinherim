<?php

namespace App\Services;

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

    public function updatePassword(array $data): bool
    {
        $user = auth()->user();
        
        return $this->repository->update($user, [
            'password' => Hash::make($data['password'])
        ]);
    }

    public function updateProfile(array $data): bool
    {
        $data['enable_notification'] = isset($data['enable_notification']) && $data['enable_notification'] == 'true' ? true : false;
        
        $user = auth()->user();
        
        return $this->repository->update($user, $data);
    }

    public function updateAvatar(array $data): void
    {        
        $user = auth()->user();

        if ($user->hasAvatar()) {
            Storage::delete($user->avatar);
        }

        $avatar = Storage::put('public/avatar', $data['file']);
        
        $user->avatar = $avatar;

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
        $enable_notification = isset($data['enable_notification']) && $data['enable_notification'] == 'true' ? true : false;

        return $this->repository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'enable_notification' => $enable_notification
        ]);
    }
}
