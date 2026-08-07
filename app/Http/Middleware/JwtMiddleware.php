<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\JwtService;
use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return ApiResponse::unauthorized();
        }

        $decoded = $this->jwtService->verify($token);

        if (!$decoded) {
            if ($this->jwtService->isExpired($token)) {
                return ApiResponse::error(ApiErrorCodes::TOKEN_EXPIRED, ApiMessages::TOKEN_EXPIRED, 401);
            }
            return ApiResponse::error(ApiErrorCodes::TOKEN_INVALID, ApiMessages::TOKEN_INVALID, 401);
        }

        if ($decoded->type !== 'access') {
            return ApiResponse::error(ApiErrorCodes::TOKEN_INVALID, ApiMessages::TOKEN_INVALID, 401);
        }

        $user = User::find($decoded->sub);

        if (!$user || $user->deleted_at !== null) {
            return ApiResponse::unauthorized();
        }

        $request->attributes->set('jwt_user', $user);
        $request->attributes->set('jwt_guard', $decoded->guard ?? 'user');
        $request->attributes->set('jwt_payload', $decoded);

        if (!empty($roles) && !in_array($user->role, $roles)) {
            return ApiResponse::forbidden();
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }
}
