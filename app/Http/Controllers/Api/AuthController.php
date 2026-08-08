<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\JwtService;
use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:128|unique:s_users,email',
            'password' => 'required|string|min:6',
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
            'role' => 'Anggota',
        ]);

        $user->created_by = $user->id;
        $user->save();

        $tokenData = $this->jwtService->issueTokenPair($this->buildTokenPayload($user));

        return ApiResponse::success([
            'user' => $this->formatUserResponse($user),
            'token' => $tokenData,
        ], ApiMessages::REGISTER_SUCCESS, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'credentials' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $user = User::where('email', strtolower($request->credentials))
            ->whereNull('deleted_at')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error(ApiErrorCodes::UNAUTHORIZED, ApiMessages::LOGIN_FAILED, 401);
        }

        $options = $this->resolveDebugOptions($request);

        $tokenData = $this->jwtService->issueTokenPair($this->buildTokenPayload($user), $options);

        return ApiResponse::success([
            'user' => $this->formatUserResponse($user),
            'token' => $tokenData,
        ], ApiMessages::LOGIN_SUCCESS);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('jwt_user');

        return ApiResponse::success([
            'id' => $user->id,
            'user_code' => $user->user_code,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $decoded = $this->jwtService->verify($request->refresh_token);

        if (!$decoded || $decoded->type !== 'refresh') {
            return ApiResponse::error(ApiErrorCodes::TOKEN_INVALID, ApiMessages::TOKEN_INVALID, 401);
        }

        $user = User::find($decoded->sub);

        if (!$user || $user->deleted_at !== null) {
            return ApiResponse::unauthorized();
        }

        $tokenData = $this->jwtService->issueTokenPair($this->buildTokenPayload($user));

        return ApiResponse::success(['token' => $tokenData], ApiMessages::TOKEN_REFRESHED);
    }

    public function logout(): JsonResponse
    {
        return ApiResponse::success(null, ApiMessages::LOGOUT_SUCCESS);
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

    private function buildTokenPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ];
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

    private function resolveDebugOptions(Request $request): array
    {
        if (!config('app.debug') || !$request->boolean('is_debug')) {
            return [];
        }

        return [
            'is_debug' => true,
            'access_token_ttl' => $request->input('access_token_ttl', 900),
            'refresh_token_ttl' => $request->input('refresh_token_ttl', 604800),
        ];
    }
}
