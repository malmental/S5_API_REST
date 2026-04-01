<?php

namespace App\Providers;

use App\Models\Incidence;
use App\Policies\IncidencePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Disable Scribe in production environment
        if (app()->environment('production')) {
            config(['scribe.enabled' => false]);
        }
    }
}
