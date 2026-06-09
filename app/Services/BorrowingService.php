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
                    throw new \RuntimeException(__('borrowing.out_of_stock'));
                }
            }

            $borrowing = Borrowing::create([
                'user_id' => $userId,
                'borrow_date' => now()->toDateString(),
                'duration_days' => $data['duration_days'],
            ]);

            Book::whereIn('id', $data['book_ids'])->decrement('stock');

            $borrowing->details()->createMany(
                collect($data['book_ids'])->map(fn($bookId) => [
                    'book_id' => $bookId,
                ])->all()
            );

            Cart::where('user_id', $userId)
                ->whereIn('book_id', $data['book_ids'])
                ->delete();

            return $borrowing->load('details.book');
        });
    }
}
