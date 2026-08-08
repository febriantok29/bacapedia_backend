<?php

namespace App\Http\Responses;

use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = ApiMessages::SUCCESS_GENERIC,
        int $statusCode = 200,
        array $metadata = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($metadata)) {
            $response['metadata'] = $metadata;
        }

        return response()->json($response, $statusCode);
    }

    public static function error(
        string $errorCode,
        string $message = 'Terjadi kesalahan',
        int $statusCode = 400,
        $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'error_code' => $errorCode,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    public static function paginated(LengthAwarePaginator $paginated, string $message = ApiMessages::SUCCESS_DATA_RETRIEVED): JsonResponse
    {
        return self::success(
            $paginated->items(),
            $message,
            200,
            [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ]
        );
    }

    public static function validationError($errors, ?string $message = null): JsonResponse
    {
        return self::error(
            ApiErrorCodes::VALIDATION_ERROR,
            $message ?? ApiMessages::VALIDATION_FAILED,
            422,
            $errors
        );
    }

    public static function unauthorized(?string $message = null): JsonResponse
    {
        return self::error(ApiErrorCodes::UNAUTHORIZED, $message ?? ApiMessages::UNAUTHORIZED, 401);
    }

    public static function forbidden(?string $message = null): JsonResponse
    {
        return self::error(ApiErrorCodes::FORBIDDEN, $message ?? ApiMessages::FORBIDDEN, 403);
    }

    public static function notFound(?string $message = null): JsonResponse
    {
        return self::error(ApiErrorCodes::NOT_FOUND, $message ?? ApiMessages::NOT_FOUND, 404);
    }
}
