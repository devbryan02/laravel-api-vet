<?php

use App\Features\Pet\Controllers\PetController;
use App\Features\PetImage\Controllers\PetImageController;
use App\Features\Stats\Controllers\StatsController;
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

Route::prefix('stats')->group(function () {
    Route::get('dashboard', [StatsController::class, 'dashboard']);
    Route::get('vaccine-alerts', [StatsController::class, 'vaccineAlerts']);
    Route::get('unvaccinated', [StatsController::class, 'unvaccinated']);
    Route::get('monthly-activity', [StatsController::class, 'monthlyActivity']);
    Route::get('species-distribution', [StatsController::class, 'speciesDistribution']);
});
