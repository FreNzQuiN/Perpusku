<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Cart;
use App\Models\BorrowingDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = $request->user();

            $books = Book::query()
                ->addSelect([
                    'cart_id' => Cart::select('id')
                        ->whereColumn('book_id', 'books.id')
                        ->where('user_id', $user->id)
                        ->limit(1),
                ])
                ->when($request->filled('title'), function ($query) use ($request) {
                    if (DB::getDriverName() === 'mysql') {
                        $query->whereFullText('title', $request->title);
                    } else {
                        $query->where('title', 'LIKE', '%' . $request->title . '%');
                    }
                })
                ->paginate(20);

            $bookIds = $books->pluck('id');

            $borrowings = BorrowingDetail::whereIn('book_id', $bookIds)
                ->whereHas('borrowing', fn($q) => $q->where('user_id', $user->id))
                ->with('borrowing')
                ->get()
                ->keyBy('book_id');

            $books->getCollection()->transform(function ($book) use ($borrowings) {
                $book->in_cart = $book->cart_id !== null;
                $book->in_borrowing = isset($borrowings[$book->id]);
                if (isset($borrowings[$book->id])) {
                    $detail = $borrowings[$book->id];
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
        } catch (\Exception $e) {
            Log::error('Gagal memuat data buku: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data buku'
            ], 500);
        }
    }
}
