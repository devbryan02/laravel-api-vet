<?php

namespace App\Providers;

use App\Features\Pet\Models\Pet;
use App\Observers\PetObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Pet::observe(PetObserver::class);
    }
}
