<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updatePassword(array $data): bool
    {
        $user = auth()->user();
        
        return $user->update([
            'password' => Hash::make($data['password'])
        ]);
    }

    public function updateProfile(array $data): bool
    {
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
}
