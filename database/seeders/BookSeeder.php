<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'stock' => 5
        ]);

        \App\Models\Book::create([
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'stock' => 3
        ]);

        \App\Models\Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'stock' => 10
        ]);

        \App\Models\Book::create([
            'title' => 'Brave New World',
            'author' => 'Aldous Huxley',
            'stock' => 7
        ]);
    }
}
