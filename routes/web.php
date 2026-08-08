<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [WebController::class, 'login'])->name('login');
Route::post('/login', [WebController::class, 'doLogin']);
Route::get('/register', [WebController::class, 'register']);
Route::post('/register', [WebController::class, 'doRegister']);
Route::post('/logout', [WebController::class, 'logout']);

Route::middleware('panel.auth')->group(function () {
    Route::get('/', [WebController::class, 'dashboard']);
    Route::get('/books', [WebController::class, 'books']);
    Route::get('/categories', [WebController::class, 'categories']);
    Route::get('/borrows', [WebController::class, 'borrows']);
    Route::get('/borrows/{id}', [WebController::class, 'borrowDetail']);
    Route::post('/borrows', [WebController::class, 'storeBorrow']);

    Route::middleware('panel.auth:Admin,Petugas')->group(function () {
        Route::post('/borrows/{id}/return', [WebController::class, 'returnBorrow']);

        Route::post('/books', [WebController::class, 'storeBook']);
        Route::post('/books/{id}/update', [WebController::class, 'updateBook']);
        Route::post('/books/{id}/delete', [WebController::class, 'deleteBook']);
        Route::post('/categories', [WebController::class, 'storeCategory']);
        Route::post('/categories/{id}/update', [WebController::class, 'updateCategory']);
        Route::post('/categories/{id}/delete', [WebController::class, 'deleteCategory']);
    });

    Route::middleware('panel.auth:Admin')->group(function () {
        Route::get('/users', [WebController::class, 'users']);
        Route::post('/users', [WebController::class, 'storeUser']);
        Route::post('/users/{id}/update', [WebController::class, 'updateUser']);
        Route::post('/users/{id}/delete', [WebController::class, 'deleteUser']);
        Route::post('/users/{id}/reset-password', [WebController::class, 'resetUserPassword']);
    });
});
