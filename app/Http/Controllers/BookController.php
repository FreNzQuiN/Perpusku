<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('title')) {
            $searchTerm = str_replace(' ', '%', $request->title);
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        return response()->json($query->get());
    }
}
