<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Cart;
use App\Models\BorrowingDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('title')) {
            if (DB::getDriverName() === 'mysql') {
                $query->whereFullText('title', $request->title);
            } else {
                $query->where('title', 'LIKE', '%' . $request->title . '%');
            }
        }

        $books = $query->paginate(20);
        $user = $request->user();
        $bookIds = $books->pluck('id');

        $cartItems = Cart::where('user_id', $user->id)
            ->whereIn('book_id', $bookIds)
            ->get()
            ->keyBy('book_id');

        $borrowings = BorrowingDetail::whereIn('book_id', $bookIds)
            ->whereHas('borrowing', fn(Builder $q) => $q->where('user_id', $user->id))
            ->with('borrowing')
            ->get()
            ->keyBy('book_id');

        $books->getCollection()->transform(function ($book) use ($cartItems, $borrowings) {
            $book->in_cart = isset($cartItems[$book->id]);
            $book->cart_id = $cartItems[$book->id]->id ?? null;
            if (isset($borrowings[$book->id])) {
                $detail = $borrowings[$book->id];
                $book->in_borrowing = true;
                $book->borrowing = [
                    'borrow_date' => $detail->borrowing->borrow_date,
                    'duration_days' => $detail->borrowing->duration_days,
                    'return_date' => \Carbon\Carbon::parse($detail->borrowing->borrow_date)
                        ->addDays($detail->borrowing->duration_days)->format('Y-m-d'),
                ];
            }
            return $book;
        });

        return BookResource::collection($books);
    }
}
