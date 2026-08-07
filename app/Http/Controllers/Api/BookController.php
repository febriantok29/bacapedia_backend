<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Book::with('category:id,name');

        if ($user->role === 'Admin' && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('published_year')) {
            $query->where('published_year', $request->published_year);
        }

        $books = $query->orderBy('title')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($books);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Book::with('category:id,name');

        if ($user->role === 'Admin' && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $book = $query->find($id);

        if (!$book) {
            return ApiResponse::notFound(ApiMessages::BOOK_NOT_FOUND);
        }

        return ApiResponse::success($book);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== 'Admin') {
            return ApiResponse::forbidden();
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid|exists:m_categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'published_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $bookCode = $this->generateBookCode();

        $book = Book::create([
            'book_code' => $bookCode,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'published_year' => $request->published_year,
            'stock' => $request->stock,
        ]);

        $book->created_by = $user->id;
        $book->save();
        $book->load('category:id,name');

        return ApiResponse::success($book, ApiMessages::DATA_CREATED, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== 'Admin') {
            return ApiResponse::forbidden();
        }

        $book = Book::find($id);

        if (!$book) {
            return ApiResponse::notFound(ApiMessages::BOOK_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|uuid|exists:m_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'publisher' => 'sometimes|required|string|max:255',
            'published_year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $book->fill($request->only([
            'category_id', 'title', 'author', 'publisher', 'published_year', 'stock',
        ]));
        $book->updated_by = $user->id;
        $book->save();
        $book->load('category:id,name');

        return ApiResponse::success($book, ApiMessages::DATA_UPDATED);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== 'Admin') {
            return ApiResponse::forbidden();
        }

        $book = Book::find($id);

        if (!$book) {
            return ApiResponse::notFound(ApiMessages::BOOK_NOT_FOUND);
        }

        $book->deleted_by = $user->id;
        $book->save();
        $book->delete();

        return ApiResponse::success(null, ApiMessages::DATA_DELETED);
    }

    private function generateBookCode(): string
    {
        $year = now()->format('Y');
        $prefix = "BK-{$year}-";

        $lastBook = Book::where('book_code', 'like', "{$prefix}%")
            ->orderByDesc('book_code')
            ->first();

        $nextNumber = $lastBook
            ? (int) substr($lastBook->book_code, -5) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
