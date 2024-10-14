<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            // Roles
            'view-roles',
            'create-role',
            'update-role',
            'delete-role',
            'assign-permissions',

            // Permissions
            'view-permissions',
            'create-permission',
            'update-permission',
            'delete-permission',

            // Users
            'view-users',
            'create-user',
            'update-user',
            'delete-user',
            'assign-role',
            'assign-permissions',
        ];

        // Create permissions
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['description' => ucfirst(str_replace('-', ' ', $perm))]);
        }

        // Create roles
        $userRole = Role::firstOrCreate(['name' => Role::ROLE_USER]);
        $adminRole = Role::firstOrCreate(['name' => Role::ROLE_SUPER_ADMIN]);
        $managerRole = Role::firstOrCreate(['name' => Role::ROLE_EDITOR]);

        // Assign permissions to roles
        $adminRole->permissions()->sync(Permission::all()->pluck('id')->toArray());

        $managerPermissions = Permission::whereIn('name', [
            'view-roles', 'view-permissions',
            'view-users', 'create-user', 'update-user'
        ])->pluck('id')->toArray();

        $managerRole->permissions()->sync($managerPermissions);

        $userRole->permissions()->sync([]);

        // Create an admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => Role::ROLE_SUPER_ADMIN,
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Assign all role permissions to admin
        $admin->permissions()->sync($adminRole->permissions->pluck('id')->toArray());
    }
}
