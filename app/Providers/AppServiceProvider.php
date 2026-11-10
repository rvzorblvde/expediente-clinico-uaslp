<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Expediente;
use App\Observers\ExpedienteObserver;
use App\Models\Medicamento;
use App\Observers\MedicamentoObserver;
use App\Models\Farmacovigilancia;
use App\Observers\FarmacovigilanciaObserver;
use App\Models\Auditoria;

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
        // 1. Registro de los Observers de auditoría
        User::observe(UserObserver::class);
        Expediente::observe(ExpedienteObserver::class);
        Medicamento::observe(MedicamentoObserver::class);
        Farmacovigilancia::observe(FarmacovigilanciaObserver::class);

        // 2. Gates de permisos y roles
        Gate::define('is-admin', function (User $user) {
            return $user->isResponsableSanitario();
        });

        Gate::define('manage-clinical-data', function (User $user) {
            return $user->idRol == 1 || $user->idRol == 2;
        });

        // 3. Auditoría: Inicio de sesión
        Event::listen(Login::class, function (Login $event) {
            $usuario = $event->user;
            $nombreCompleto = trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellido1 ?? '') . ' ' . ($usuario->apellido2 ?? ''));

            Auditoria::create([
                'idUsuario'     => $usuario->idUsuario ?? $usuario->id ?? 1,
                'accion'        => 'Inicio de sesión',
                'tablaAfectada' => 'users',
                'detalles'      => [
                    'tipo'        => 'login',
                    'usuario'     => !empty($nombreCompleto) ? $nombreCompleto : ($usuario->email ?? 'Usuario'),
                    'ip'          => request()->ip(),
                    'dispositivo' => request()->userAgent(),
                ],
                'fechaAccion'   => now(),
            ]);
        });

        // 4. Auditoría: Cierre de sesión (Manual vs Inactividad)
        Event::listen(Logout::class, function (Logout $event) {
            $usuario = $event->user;
            $esInactividad = request()->input('motivo') === 'inactividad';
            $accionTexto = $esInactividad ? 'Cierre de sesión por inactividad' : 'Cierre de sesión manual';
            $nombreCompleto = $usuario ? trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellido1 ?? '') . ' ' . ($usuario->apellido2 ?? '')) : '';

            Auditoria::create([
                'idUsuario'     => $usuario->idUsuario ?? $usuario->id ?? 1,
                'accion'        => $accionTexto,
                'tablaAfectada' => 'users',
                'detalles'      => [
                    'tipo'        => $esInactividad ? 'inactividad' : 'manual',
                    'usuario'     => !empty($nombreCompleto) ? $nombreCompleto : ($usuario->email ?? 'Usuario'),
                    'motivo'      => $esInactividad ? 'Sesión finalizada automáticamente tras 10 minutos de inactividad' : 'Cierre de sesión voluntario',
                    'ip'          => request()->ip(),
                ],
                'fechaAccion'   => now(),
            ]);
        });
    }
}