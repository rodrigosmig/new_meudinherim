<?php

use App\Models\User;
use Illuminate\Database\Seeder;
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
        User::create([
            'name'      => 'Rodrigo Miguel',
            'email'     => 'rodrigo@gmail.com',
            'password'  => Hash::make('12345678'),
        ]);

        User::create([
            'name'      => 'Isabela Prado',
            'email'     => 'isabela@gmail.com',
            'password'  => Hash::make('12345678'),
        ]);
    }
}
