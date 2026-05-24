<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('book')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CartResource::collection($cartItems)
        ]);
    }

    public function store(StoreCartRequest $request): JsonResponse
    {
        $cart = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'book_id' => $request->book_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Added to cart',
            'data' => new CartResource($cart->load('book'))
        ], 201);
    }

    public function destroy(Request $request, $cartId): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->findOrFail($cartId);

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from cart'
        ]);
    }
}
