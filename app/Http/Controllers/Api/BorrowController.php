<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\NormalizesFilterValues;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Borrow;
use App\Models\User;
use App\Services\BorrowService;
use App\Support\ApiMessages;
use App\Support\Enums\BorrowStatus;
use App\Support\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BorrowController extends Controller
{
    use NormalizesFilterValues;

    private BorrowService $borrowService;

    public function __construct(BorrowService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Borrow::with(['book:id,book_code,title,author', 'user:id,user_code,name']);

        if ($user->role === UserRole::MEMBER->value) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }
        }

        $statuses = $this->normalizeFilter($request->input('status'));
        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        if ($request->boolean('is_overdue')) {
            $query->where('status', BorrowStatus::ACTIVE->value)
                  ->where('due_date', '<', Carbon::today());
        }

        $borrows = $query->orderByDesc('created_at')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($borrows);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        $query = Borrow::with(['book:id,book_code,title,author,publisher', 'user:id,user_code,name,email', 'histories']);

        if (in_array($user->role, [UserRole::ADMIN->value, UserRole::OFFICER->value]) && $request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $borrow = $query->find($id);

        if (!$borrow) {
            return ApiResponse::notFound(ApiMessages::BORROW_NOT_FOUND);
        }

        if ($user->role === UserRole::MEMBER->value && $borrow->user_id !== $user->id) {
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

        if (in_array($user->role, [UserRole::ADMIN->value, UserRole::OFFICER->value]) && $request->has('user_id')) {
            $targetUser = User::find($request->user_id);
            if (!$targetUser) {
                return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
            }
            $borrowUser = $targetUser;
        }

        $result = $this->borrowService->borrow($borrowUser, $request->book_id, $user->id, [
            'borrow_date' => $request->input('borrow_date'),
        ]);

        if (!$result['success']) {
            return ApiResponse::error($result['code'], $result['message'], $result['status']);
        }

        return ApiResponse::success($result['data'], ApiMessages::BORROW_SUCCESS, 201);
    }

    public function returnBook(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if (!in_array($user->role, [UserRole::ADMIN->value, UserRole::OFFICER->value])) {
            return ApiResponse::forbidden(ApiMessages::RETURN_FORBIDDEN);
        }

        $result = $this->borrowService->returnBook($id, $user->id);

        if (!$result['success']) {
            return ApiResponse::error($result['code'], $result['message'], $result['status']);
        }

        return ApiResponse::success($result['data'], ApiMessages::RETURN_SUCCESS);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        if ($user->role !== UserRole::ADMIN->value) {
            return ApiResponse::forbidden();
        }

        $borrow = Borrow::find($id);

        if (!$borrow) {
            return ApiResponse::notFound(ApiMessages::BORROW_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'borrow_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|in:' . implode(',', array_column(BorrowStatus::cases(), 'value')),
            'penalty' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $borrow->fill($request->only(['borrow_date', 'due_date', 'return_date', 'status', 'penalty']));
        $borrow->updated_by = $user->id;
        $borrow->save();

        $borrow->load(['book:id,book_code,title,author', 'user:id,user_code,name']);

        return ApiResponse::success($borrow, ApiMessages::DATA_UPDATED);
    }

    public function summary(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');
        $query = Borrow::query();

        if ($user->role === UserRole::MEMBER->value) {
            $query->where('user_id', $user->id);
        }

        $totalActive = (clone $query)->where('status', BorrowStatus::ACTIVE->value)->count();
        $totalOverdue = (clone $query)->where('status', BorrowStatus::ACTIVE->value)
            ->where('due_date', '<', Carbon::today())
            ->count();
        $totalReturned = (clone $query)->where('status', BorrowStatus::RETURNED->value)->count();
        $totalLate = (clone $query)->where('status', BorrowStatus::OVERDUE->value)->count();
        $totalPenaltyCollected = (clone $query)->where('penalty', '>', 0)->sum('penalty');

        return ApiResponse::success([
            'total_active' => $totalActive,
            'total_overdue' => $totalOverdue,
            'total_returned' => $totalReturned,
            'total_late_returned' => $totalLate,
            'total_penalty_collected' => (float) $totalPenaltyCollected,
        ]);
    }
}
