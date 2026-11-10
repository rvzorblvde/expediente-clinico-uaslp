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
        // Gate para Administrador (Responsable Sanitario / idRol == 1)
        Gate::define('manage-users', function (User $user) {
            return $user->isResponsableSanitario();
        });

        // Gate para Trabajadores y Administradores (idRol 1 y 2)
        Gate::define('manage-clinical-data', function (User $user) {
            return $user->idRol == 1 || $user->idRol == 2;
        });
    }
}