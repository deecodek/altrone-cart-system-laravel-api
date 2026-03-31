<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'John Customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );

        User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );

        User::factory()->count(10)->create();
    }
}
