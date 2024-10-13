<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::create([
            'name' => 'F. Scott Fitzgerald',
            'biography' => 'An American novelist and short story writer.',
            'birthdate' => '1896-09-24',
            'image' => 'http://127.0.0.1:8000/assets/images/authors/fitzgerald.png',
        ]);

        Author::create([
            'name' => 'George Orwell',
            'biography' => 'An English novelist, essayist, journalist, and critic.',
            'birthdate' => '1903-06-25',
            'image' => 'http://127.0.0.1:8000/assets/images/authors/orwell.png',
        ]);

        Author::create([
            'name' => 'Harper Lee',
            'biography' => 'An American novelist widely known for "To Kill a Mockingbird".',
            'birthdate' => '1926-04-28',
            'image' => 'http://127.0.0.1:8000/assets/images/authors/lee.png',
        ]);
    }
}
