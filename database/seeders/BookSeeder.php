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
        $books = [
            ['title' => 'Bumi Manusia',              'author' => 'Pramoedya Ananta Toer',    'stock' => 8],
            ['title' => 'Laut Bercerita',            'author' => 'Leila S. Chudori',          'stock' => 5],
            ['title' => 'Pulang',                    'author' => 'Tere Liye',                 'stock' => 12],
            ['title' => 'Catatan Juang',             'author' => 'Andrea Hirata',             'stock' => 3],
            ['title' => 'Ronggeng Dukuh Paruk',      'author' => 'Ahmad Tohari',              'stock' => 6],
            ['title' => 'Negeri 5 Menara',           'author' => 'Ahmad Fuadi',               'stock' => 10],
            ['title' => 'Senja Hujan & Cerita yang Telah Usai', 'author' => 'Birggitania',    'stock' => 7],
            ['title' => 'Hujan',                     'author' => 'Tere Liye',                 'stock' => 4],
            ['title' => 'Filosofi Teras',            'author' => 'Henry Manampiring',         'stock' => 9],
            ['title' => 'Atomic Habits',             'author' => 'James Clear',               'stock' => 15],
            ['title' => 'Sapiens: A Brief History of Humankind', 'author' => 'Yuval Noah Harari', 'stock' => 2],
            ['title' => 'Laskar Pelangi',            'author' => 'Andrea Hirata',             'stock' => 11],
        ];

        foreach ($books as $book) {
            \App\Models\Book::create($book);
        }
    }
}
