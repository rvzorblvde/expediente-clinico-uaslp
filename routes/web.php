<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicamentoController;

// Public Routes (Authentication)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/pw-reset-request', [AuthController::class, 'pwResetRequest'])->name('password-reset');

// Protected Routes (Authorized Users)

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Medical records management
    Route::get('/expedientes', [ExpedienteController::class, 'index'])->name('expedientes.index');
    Route::get('/expedientes/crear', [ExpedienteController::class, 'create'])->name('expedientes.create');
    Route::post('/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
    Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'show'])->name('expedientes.show');
    Route::get('/expedientes/{expediente}/editar', [ExpedienteController::class, 'edit'])->name('expedientes.edit');
    Route::put('/expedientes/{expediente}', [ExpedienteController::class, 'update'])->name('expedientes.update');
    Route::delete('/expedientes/{expediente}', [ExpedienteController::class, 'destroy'])->name('expedientes.destroy');
    Route::patch('/expedientes/{expediente}/restaurar', [ExpedienteController::class, 'restore'])->name('expedientes.restore');

    //Medicamentos

    Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos.index');
    Route::get('/medicamentos/crear', [MedicamentoController::class, 'create'])->name('medicamentos.create');
    Route::post('/medicamentos', [MedicamentoController::class, 'store'])->name('medicamentos.store');
    Route::get('/medicamentos/{medicamento}/editar', [MedicamentoController::class, 'edit'])->name('medicamentos.edit');
    Route::put('/medicamentos/{medicamento}', [MedicamentoController::class, 'update'])->name('medicamentos.update');
    Route::delete('/medicamentos/{medicamento}', [MedicamentoController::class, 'destroy'])->name('medicamentos.destroy');

    

    // User administration (Health Officer Only)
    Route::middleware(['can:manage-users'])->group(function () {
        Route::resource('/usuarios', UserController::class)->except('show');
        Route::put('/usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Cambio de contraseña propio (cualquier usuario autenticado)
    Route::get('/mi-perfil/cambiar-contrasena', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/mi-perfil/cambiar-contrasena', [UserController::class, 'updatePassword'])->name('password.update');
});
