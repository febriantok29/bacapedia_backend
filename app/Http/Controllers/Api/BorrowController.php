<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Borrow;
use App\Models\User;
use App\Services\BorrowService;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BorrowController extends Controller
{
    private BorrowService $borrowService;

    public function __construct(BorrowService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Borrow::with(['book:id,book_code,title,author', 'user:id,user_code,name']);

        if ($user->role === 'Anggota') {
            $query->where('user_id', $user->id);
        } else {
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $borrows = $query->orderByDesc('created_at')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($borrows);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Borrow::with(['book:id,book_code,title,author,publisher', 'user:id,user_code,name,email', 'histories']);

        if (in_array($user->role, ['Admin', 'Petugas']) && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $borrow = $query->find($id);

        if (!$borrow) {
            return ApiResponse::notFound(ApiMessages::BORROW_NOT_FOUND);
        }

        if ($user->role === 'Anggota' && $borrow->user_id !== $user->id) {
            return ApiResponse::forbidden();
        }

        return ApiResponse::success($borrow);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $validator = Validator::make($request->all(), [
            'book_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $borrowUser = $user;

        if (in_array($user->role, ['Admin', 'Petugas']) && $request->has('user_id')) {
            $targetUser = User::find($request->user_id);
            if (!$targetUser) {
                return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
            }
            $borrowUser = $targetUser;
        }

        $result = $this->borrowService->borrow($borrowUser, $request->book_id, $user->id);

        if (!$result['success']) {
            return ApiResponse::error($result['code'], $result['message'], $result['status']);
        }

        return ApiResponse::success($result['data'], ApiMessages::BORROW_SUCCESS, 201);
    }

    public function returnBook(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if (!in_array($user->role, ['Admin', 'Petugas'])) {
            return ApiResponse::forbidden(ApiMessages::RETURN_FORBIDDEN);
        }

        $result = $this->borrowService->returnBook($id, $user->id);

        if (!$result['success']) {
            return ApiResponse::error($result['code'], $result['message'], $result['status']);
        }

        return ApiResponse::success($result['data'], ApiMessages::RETURN_SUCCESS);
    }
}
