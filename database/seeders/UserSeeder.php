<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'ZINSOU Flooride',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            
        ]);

        User::create([
            'name' => 'ADANDE Jean',
            'email' => 'client@gmail.com',
            'password' => Hash::make('password'),
         
        ]);
    }
}