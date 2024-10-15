<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role_id' => Role::ROLE_SUPER_ADMIN,
            'is_active' => true,
            'token' => Str::random(60),
            'token_expiration' => now()->addDays(7),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
            'role_id' => Role::ROLE_USER,
            'is_active' => true,
            'token' => Str::random(60),
            'token_expiration' => now()->addDays(7),
        ]);

        User::create([
            'name' => 'Michael Johnson',
            'email' => 'michael@example.com',
            'password' => bcrypt('password123'),
            'role_id' => Role::ROLE_GUEST,
            'is_active' => false,
            'token' => Str::random(60),
            'token_expiration' => now()->addDays(7),
        ]);

    }
}
