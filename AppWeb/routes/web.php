<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas exclusivas para invitados (usuarios no autenticados)
Route::middleware('guest')->group(function () {
    // Rutas para inicio de sesión
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); // Ruta para mostrar el formulario de inicio de sesión
    Route::get('/login', [AuthController::class, 'showLoginForm']); // Ruta para mostrar el formulario de inicio de sesión
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt'); // Ruta para procesar el inicio de sesión
});

// Rutas protegidas (sólo usuarios con sesión activa)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Ruta para cerrar sesión

    // Panel Administrador
    Route::get('/admin', function () {
        return 'Panel del Administrador - Bienvenido, ' . auth()->user()->name;
    })->name('admin.home');
    // Panel Recepcionista
    Route::get('/recepcion', function () {
        return 'Panel de Recepción - Bienvenido, ' . auth()->user()->name;
    })->name('receptionist.home');
    // Panel Técnico
    Route::get('/tecnico', function () {
        return 'Panel del Taller / Técnico - Bienvenido, ' . auth()->user()->name;
    })->name('technician.home');

});

// Ruta pública de error 404
Route::fallback(function () {
    return response()->view('public.not-found', [], 404);
});