<?php

use App\Features\User\Controllers\AuthController;
use App\Features\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::get('user', [AuthController::class, 'me'])->middleware('auth:api');

Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::middleware('role:ADMIN')->group(function () {
        Route::get('veterinarians', [UserController::class, 'veterinarians']);
        Route::post('veterinarians', [UserController::class, 'storeVeterinarian']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::patch('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
    });

    Route::middleware('role:VETERINARIAN,ADMIN')->group(function () {
        Route::get('owners', [UserController::class, 'owners']);
        Route::post('owners', [UserController::class, 'storeOwner']);
        Route::get('{user}', [UserController::class, 'show']);
    });
});
