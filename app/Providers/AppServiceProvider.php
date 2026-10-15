<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        // Definir la regla (Gate) para gestionar usuarios (Solo Responsable Sanitario / idRol == 1)
        Gate::define('manage-users', function (User $user) {
            return $user->isResponsableSanitario();
        });
    }
}