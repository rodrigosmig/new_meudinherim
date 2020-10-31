<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
}
