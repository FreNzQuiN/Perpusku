<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    public function createBorrowing(array $data, int $userId): Borrowing
    {
        return DB::transaction(function () use ($data, $userId) {
            $books = Book::whereIn('id', $data['book_ids'])
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($data['book_ids'] as $bookId) {
                $book = $books->get($bookId);
                if (!$book || $book->stock <= 0) {
                    throw new \RuntimeException('Maaf, beberapa buku tidak tersedia');
                }
            }

            $borrowing = Borrowing::create([
                'user_id' => $userId,
                'borrow_date' => $data['borrow_date'],
                'duration_days' => $data['duration_days'],
            ]);

            $details = [];
            foreach ($books as $bookId => $book) {
                $book->decrement('stock');
                $details[] = [
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $bookId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('borrowing_details')->insert($details);

            Cart::where('user_id', $userId)
                ->whereIn('book_id', $data['book_ids'])
                ->delete();

            return $borrowing->load('details.book');
        });
    }
}
