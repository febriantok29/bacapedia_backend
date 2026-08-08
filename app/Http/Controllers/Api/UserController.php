<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\NormalizesFilterValues;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use NormalizesFilterValues;

    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%");
            });
        }

        $roles = $this->normalizeFilter($request->input('role'));
        if ($roles) {
            $query->whereIn('role', $roles);
        }

        $users = $query->orderBy('name')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($users);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::withCount('borrows')->find($id);

        if (!$user) {
            return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
        }

        return ApiResponse::success($user);
    }

    public function store(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('jwt_user');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:128|unique:s_users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Admin,Petugas,Anggota',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $userCode = $this->generateUserCode();

        $user = User::create([
            'user_code' => $userCode,
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $user->created_by = $admin->id;
        $user->save();

        return ApiResponse::success($this->formatUserResponse($user), ApiMessages::DATA_CREATED, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $admin = $request->attributes->get('jwt_user');

        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:128|unique:s_users,email,' . $id,
            'role' => 'sometimes|required|in:Admin,Petugas,Anggota',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = strtolower($request->email);
        }
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->updated_by = $admin->id;
        $user->save();

        return ApiResponse::success($this->formatUserResponse($user), ApiMessages::DATA_UPDATED);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $admin = $request->attributes->get('jwt_user');

        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
        }

        if ($user->id === $admin->id) {
            return ApiResponse::error(ApiErrorCodes::CONFLICT, ApiMessages::CANNOT_DELETE_SELF, 409);
        }

        $user->deleted_by = $admin->id;
        $user->save();
        $user->delete();

        return ApiResponse::success(null, ApiMessages::DATA_DELETED);
    }

    public function resetPassword(Request $request, string $id): JsonResponse
    {
        $admin = $request->attributes->get('jwt_user');

        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound(ApiMessages::USER_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $user->password = Hash::make($request->password);
        $user->updated_by = $admin->id;
        $user->save();

        return ApiResponse::success(null, ApiMessages::PASSWORD_RESET_SUCCESS);
    }

    private function generateUserCode(): string
    {
        $prefix = 'USR-';

        $lastUser = User::where('user_code', 'like', $prefix . '%')
            ->orderByDesc('user_code')
            ->first();

        $nextNumber = $lastUser
            ? (int) substr($lastUser->user_code, -5) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'user_code' => $user->user_code,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
