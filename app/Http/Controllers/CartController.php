<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $cartItems = Cart::where('user_id', $request->user()->id)
                ->with('book')
                ->get();

            return response()->json([
                'success' => true,
                'data' => CartResource::collection($cartItems)
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memuat keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat keranjang'
            ], 500);
        }
    }

    public function store(StoreCartRequest $request): JsonResponse
    {
        try {
            $cart = Cart::updateOrCreate(
                ['user_id' => $request->user()->id, 'book_id' => $request->book_id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Berhasil ditambahkan ke keranjang',
                'data' => new CartResource($cart->load('book'))
            ], 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan ke keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang'
            ], 500);
        }
    }

    public function destroy(Request $request, $cartId): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', $request->user()->id)->findOrFail($cartId);

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil dihapus dari keranjang'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus item dari keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item dari keranjang'
            ], 500);
        }
    }
}
