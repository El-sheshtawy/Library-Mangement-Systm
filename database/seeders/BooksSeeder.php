<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'title' => 'The Great Gatsby',
            'description' => 'A novel by F. Scott Fitzgerald',
            'lang' => 'English',
            'number_pages' => 180,
            'views_count'  => 127,
            'downloads_count'  => 107,
            'cover_image'  => 'https://example.com/great-gatsby-cover.jpg',
            'file'   => 'https://example.com/great-gatsby.pdf',
            'size' => 1.2, 
            'published_at' => now(),
            'is_approved' => true,
            'category_id' => 1,
            'author_id' => 1,    
            'user_id'  => 3,
            'book_series_id' => null,
        ]);

        Book::create([
            'title' => '1984',
            'description' => 'A novel by George Orwell',
            'lang' => 'English',
            'number_pages' => 328,
            'views_count'  => 4384,
            'downloads_count'  => 3009,
            'cover_image'  => 'https://example.com/great-gatsby-cover.jpg',
            'file'   => 'https://example.com/great-gatsby.pdf',
            'size' => 2.1, 
            'published_at' => now(),
            'is_approved' => true,
            'category_id' => 2, 
            'author_id' => 2,    
            'user_id'  => 3,
            'book_series_id' => null,
        ]);

        Book::create([
            'title' => 'To Kill a Mockingbird',
            'description' => 'A novel by Harper Lee',
            'lang' => 'English',
            'number_pages' => 281,
            'views_count'  => 2479,
            'downloads_count'  => 1470,
            'cover_image'  => 'https://example.com/great-gatsby-cover.jpg',
            'file'   => 'https://example.com/great-gatsby.pdf',
            'size' => 1.9,
            'published_at' => now(),
            'is_approved' => true,
            'category_id' => 1, 
            'author_id' => 3,   
            'user_id'  => 3,  
            'book_series_id' => null,
        ]);

    }
}
