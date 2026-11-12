<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\FarmacovigilanciaController;
use App\Http\Controllers\AuditoriaController;

// Public Routes (Authentication)
// Rutas públicas de autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // Ruta GET para las redirecciones por inactividad
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // Procesa el inicio de sesión
Route::post('/pw-reset-request', [AuthController::class, 'pwResetRequest'])->name('password-reset');

// Protected Routes (Authorized Users)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // PERMISO: TODOS (Alumnos, Trabajadores, Admin)
    Route::get('/expedientes', [ExpedienteController::class, 'index'])->name('expedientes.index');
    Route::get('/expedientes/crear', [ExpedienteController::class, 'create'])->name('expedientes.create');
    Route::post('/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');

    //Visitas
    Route::resource('visitas', VisitaController::class);

    //Medicamentos

    Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos.index');
    Route::get('/medicamentos/crear', [MedicamentoController::class, 'create'])->name('medicamentos.create');
    Route::post('/medicamentos', [MedicamentoController::class, 'store'])->name('medicamentos.store');
    Route::get('/medicamentos/{medicamento}/editar', [MedicamentoController::class, 'edit'])->name('medicamentos.edit');
    Route::put('/medicamentos/{medicamento}', [MedicamentoController::class, 'update'])->name('medicamentos.update');
    Route::delete('/medicamentos/{medicamento}', [MedicamentoController::class, 'destroy'])->name('medicamentos.destroy');

    // Farmacovigilancia
    Route::get('/farmacovigilancia', [FarmacovigilanciaController::class, 'index'])->name('farmacovigilancia.index');
    Route::get('/farmacovigilancia/crear', [FarmacovigilanciaController::class, 'create'])->name('farmacovigilancia.create');
    Route::post('/farmacovigilancia', [FarmacovigilanciaController::class, 'store'])->name('farmacovigilancia.store');
    Route::get('/farmacovigilancia/{farmacovigilancia}', [FarmacovigilanciaController::class, 'show'])->name('farmacovigilancia.show');

    // PERMISO: TRABAJADOR Y ADMIN
    Route::middleware(['can:manage-clinical-data'])->group(function () {
        // Consultar / Modificar expedientes y visitas
        Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'show'])->name('expedientes.show');
        Route::get('/expedientes/{expediente}/editar', [ExpedienteController::class, 'edit'])->name('expedientes.edit');
        Route::put('/expedientes/{expediente}', [ExpedienteController::class, 'update'])->name('expedientes.update');
        Route::delete('/expedientes/{expediente}', [ExpedienteController::class, 'destroy'])->name('expedientes.destroy');
        Route::patch('/expedientes/{expediente}/restaurar', [ExpedienteController::class, 'restore'])->name('expedientes.restore');

        // Registro y control de medicamentos
        Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos.index');
        Route::get('/medicamentos/crear', [MedicamentoController::class, 'create'])->name('medicamentos.create');
        Route::post('/medicamentos', [MedicamentoController::class, 'store'])->name('medicamentos.store');
        Route::get('/medicamentos/{medicamento}/editar', [MedicamentoController::class, 'edit'])->name('medicamentos.edit');
        Route::put('/medicamentos/{medicamento}', [MedicamentoController::class, 'update'])->name('medicamentos.update');
        Route::delete('/medicamentos/{medicamento}', [MedicamentoController::class, 'destroy'])->name('medicamentos.destroy');

        // Generación de reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::match(['get', 'post'], '/reportes/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.pdf');
        Route::match(['get', 'post'], '/reportes/excel', [ReporteController::class, 'exportarExcel'])->name('reportes.excel');

        // Edición de farmacovigilancia
        Route::get('/farmacovigilancia/{farmacovigilancia}/editar', [FarmacovigilanciaController::class, 'edit'])->name('farmacovigilancia.edit');
        Route::put('/farmacovigilancia/{farmacovigilancia}', [FarmacovigilanciaController::class, 'update'])->name('farmacovigilancia.update');
        Route::delete('/farmacovigilancia/{farmacovigilancia}', [FarmacovigilanciaController::class, 'destroy'])->name('farmacovigilancia.destroy');
    });


    // PERMISO: SOLO ADMIN (Responsable Sanitario)
    Route::middleware(['can:is-admin'])->group(function () {
        Route::resource('/usuarios', UserController::class)->except('show');
        Route::put('/usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');
    });


    // CAMBIO DE CONTRASEÑA PROPIO (Todos autenticados)
    Route::get('/mi-perfil/cambiar-contrasena', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/mi-perfil/cambiar-contrasena', [UserController::class, 'updatePassword'])->name('password.update');
});
