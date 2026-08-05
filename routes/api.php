<?php

use App\Http\Controllers\Api\V1\Auth\AuthController as V1AuthController;
use App\Http\Controllers\Api\V1\BoardController as V1BoardController;
use Illuminate\Support\Facades\Route;

// API V1
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::name('auth.')->group(function () {
        // Public routes
        Route::post('/register', [V1AuthController::class, 'register'])->name('register');
        Route::post('/login', [V1AuthController::class, 'login'])->name('login');

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::delete('/logout', [V1AuthController::class, 'logout'])->name('logout');
            Route::delete('/logout/all', [V1AuthController::class, 'logoutAllDevices'])->name('logout-all');

            Route::get('/user', [V1AuthController::class, 'user'])->name('user');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('boards', V1BoardController::class);
    });
});
