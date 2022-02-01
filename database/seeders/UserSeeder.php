<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jon = User::create([
            'name'      => 'Jon Doe',
            'email'     => 'jon@gmail.com',
            'password'  => Hash::make('12345678'),
        ]);

        $jane = User::create([
            'name'      => 'Jane Doe',
            'email'     => 'jane@gmail.com',
            'password'  => Hash::make('12345678'),
        ]);

        Auth::attempt(['email' => $jon->email, 'password' => '12345678']);
    }
}
