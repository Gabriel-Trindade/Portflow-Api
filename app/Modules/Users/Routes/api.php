<?php

use App\Modules\Users\Http\Controllers\AuthController;
use App\Modules\Users\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/users/me', [UserController::class, 'profile']);
    Route::put('/users/me', [UserController::class, 'updateProfile']);
    Route::put('/users/me/password', [UserController::class, 'changePassword']);

    // Admin-only user management
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/users/{id}/role', [UserController::class, 'assignRole'])->where('id', '[0-9]+');
        Route::delete('/users/{id}', [UserController::class, 'deactivate'])->where('id', '[0-9]+');
    });
});
