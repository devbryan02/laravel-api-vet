<?php

use App\Features\Pet\Controllers\PetController;
use App\Features\PetImage\Controllers\PetImageController;
use App\Features\Stats\Controllers\StatsController;
use App\Features\Vaccine\Controllers\VaccineController;
use Illuminate\Support\Facades\Route;

Route::pattern('user', '[0-9A-Za-z]{26}');
Route::pattern('pet', '[0-9A-Za-z]{26}');
Route::pattern('vaccine', '[0-9A-Za-z]{26}');
Route::pattern('image', '[0-9A-Za-z]{26}');

require base_path('app/Features/User/routes/api.php');

Route::middleware(['auth:api', 'role:VETERINARIAN,ADMIN'])->group(function () {
    Route::post('pets/images', [PetImageController::class, 'store']);
    Route::delete('pets/images/{image}', [PetImageController::class, 'destroy']);

    Route::get('pets/{pet}/vaccines', [VaccineController::class, 'byPet']);
    Route::apiResource('pets', PetController::class);
    Route::apiResource('vaccines', VaccineController::class);

    Route::prefix('stats')->group(function () {
        Route::get('dashboard', [StatsController::class, 'dashboard']);
        Route::get('vaccine-alerts', [StatsController::class, 'vaccineAlerts']);
        Route::get('unvaccinated', [StatsController::class, 'unvaccinated']);
        Route::get('monthly-activity', [StatsController::class, 'monthlyActivity']);
        Route::get('species-distribution', [StatsController::class, 'speciesDistribution']);
    });
});
