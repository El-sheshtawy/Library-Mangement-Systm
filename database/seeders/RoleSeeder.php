<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'SuperAdmin',
            'description' => 'Administrator with full access',
        ]);

        Role::create([
            'name' => 'Editor',
            'description' => 'Editor with access to manage content',
        ]);

        Role::create([
            'name' => 'User',
            'description' => 'Regular user with limited access',
        ]);

        Role::create([
            'name' => 'Guest',
            'description' => 'Guest user with minimal access',
        ]);
    }
}
