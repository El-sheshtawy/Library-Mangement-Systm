<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
     public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            CategoryGroupsSeeder::class,
            CategorySeeder::class,
            AuthorSeeder::class,
            BooksSeeder::class,
            CommentsSeeder::class,
        ]);
    }
}
