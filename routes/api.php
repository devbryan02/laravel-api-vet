<?php

use App\Features\Pet\Controllers\PetController;
use App\Features\PetImage\Controllers\PetImageController;
use App\Features\Vaccine\Controllers\VaccineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pets routes
Route::apiResource('pets', PetController::class);
Route::apiResource("vaccines", VaccineController::class);
Route::apiResource("pets/images", PetImageController::class);


