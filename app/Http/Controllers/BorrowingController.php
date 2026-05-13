<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Book;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $history = Borrowing::where('user_id', $request->user()->id)
            ->with('details.book')
            ->orderBy('borrow_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrow_date' => 'required|date|after_or_equal:today',
            'duration_days' => 'required|integer|min:1|max:3',
            'book_ids' => 'required|array|min:1|max:10',
            'book_ids.*' => 'exists:books,id|distinct',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if books are available (stock > 0)
        $unavailableBooks = Book::whereIn('id', $request->book_ids)
            ->where('stock', '<=', 0)
            ->pluck('title')
            ->toArray();

        if (!empty($unavailableBooks)) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, beberapa buku tidak tersedia',
                'errors' => [
                    'book_ids' => ['Buku berikut sedang habis stoknya: ' . implode(', ', $unavailableBooks)]
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'borrow_date' => $request->borrow_date,
                'duration_days' => $request->duration_days,
            ]);

            foreach ($request->book_ids as $bookId) {
                // Decrement stock (already validated above)
                $book = Book::lockForUpdate()->find($bookId);
                $book->decrement('stock');

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $bookId,
                ]);
            }

            // Optional: Clear cart after successful borrowing
            Cart::where('user_id', $request->user()->id)->whereIn('book_id', $request->book_ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Borrowing successful',
                'data' => $borrowing->load('details.book')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
