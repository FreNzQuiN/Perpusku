<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)->with('book')->get();
        return response()->json([
            'success' => true,
            'data' => $cartItems
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check stock
        $book = Book::find($request->book_id);
        if ($book->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak dapat ditambahkan',
                'errors' => ['book_id' => ['Maaf, stok buku ini sedang kosong dan tidak bisa dimasukkan ke keranjang.']]
            ], 422);
        }

        $cart = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'book_id' => $request->book_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Added to cart',
            'data' => $cart->load('book')
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $cart = Cart::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from cart'
        ]);
    }
}
