<?php

namespace App\Http\Controllers\API;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $book = Book::create($request->all());

        return response()->json(
            [
                'message' => 'book created successfully',
                'book' => $book,
            ],
            200,
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $book = Book::find($id);

        if (!$book) {
            return response()->json(
                [
                    'message' => 'book not found with id ' . $id,
                ],
                400,
            );
        }

        $book->update($request->all());

        return response()->json(
            [
                'message' => 'book updated successfully',
            ],
            200,
        );
    }

    public function list(Request $request)
    {

        $request->validate([
            /** @query */
            'page' => ['required', 'integer'],
            'perPage' => ['required', 'integer'],
        ]);

        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;

        $offset = ($page - 1) * $perPage;

        $books = Book::paginate($perPage, ['*'], 'page', $page);

        return response()->json(
            [
                'message' => 'get book list successfully',
                'books' => $books,
            ],
            200,
        );
    }

    public function delete($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(
                [
                    'message' => 'book not found',
                ],
                400,
            );
        }

        $book->delete();

        return response()->json(
            [
                'message' => 'book deleted successfully',
            ],
            200,
        );
    }
}
