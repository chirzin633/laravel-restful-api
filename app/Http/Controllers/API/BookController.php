<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    function create(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required'],
            'description' => ['nullable']
        ]);

        $book = Book::create($validated);

        return response()->json([
            'message' => 'Book created successfully!',
            'data' => $book
        ]);
    }
}
