<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::create([
            'name' => 'DM User',
            'email' => 'dm@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::DM,
        ]);

        User::create([
            'name' => 'Player One',
            'email' => 'player1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Player,
        ]);

        User::create([
            'name' => 'Player Two',
            'email' => 'player2@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Player,
        ]);
    }
}
