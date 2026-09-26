<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)

    {
        $perPage = $request->perPage ?? 5;

        $books = Book::paginate($perPage);

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data buku'
            ], 404);
        }

        return response()->json([
            'data' => $books
        ]);
    }
    public function create(Request $request)
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

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title' => ['required'],
            'description' => ['nullable']
        ]);

        $book = Book::findOrFail($id);

        if (!$book) {
            return response()->json([
                'message' => 'Tidak ada data buku'
            ], 404);
        }

        $book->update($validated);

        return response()->json([
            'message' => 'Book updated successfully!',
            'data' => $book
        ]);
    }

    public function delete(Book $book)
    {
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully!',
        ]);
    }
}
