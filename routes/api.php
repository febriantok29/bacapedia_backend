<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\UserController;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    Route::middleware('jwt')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::delete('/auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('books', BookController::class);

        Route::get('/borrows', [BorrowController::class, 'index']);
        Route::get('/borrows/summary', [BorrowController::class, 'summary']);
        Route::post('/borrows', [BorrowController::class, 'store']);
        Route::get('/borrows/{id}', [BorrowController::class, 'show']);
        Route::put('/borrows/{id}', [BorrowController::class, 'update']);
        Route::post('/borrows/{id}/return', [BorrowController::class, 'returnBook']);
    });

    Route::middleware('jwt:Admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::apiResource('configs', ConfigController::class);
    });

});

Route::fallback(function () {
    return ApiResponse::notFound('Endpoint tidak ditemukan');
});
