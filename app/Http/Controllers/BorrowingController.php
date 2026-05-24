<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Borrowing;
use App\Services\BorrowingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function __construct(
        protected BorrowingService $borrowingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $history = Borrowing::where('user_id', $request->user()->id)
            ->with('details.book')
            ->orderBy('borrow_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BorrowingResource::collection($history)
        ]);
    }

    public function store(StoreBorrowingRequest $request): JsonResponse
    {
        try {
            $borrowing = $this->borrowingService->createBorrowing(
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Borrowing successful',
                'data' => new BorrowingResource($borrowing)
            ], 201);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => ['book_ids' => [$e->getMessage()]]
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
            ], 500);
        }
    }
}
