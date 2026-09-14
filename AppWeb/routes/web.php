<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas exclusivas para invitados (usuarios no autenticados)
Route::middleware('guest')->group(function () {
    // Rutas para inicio de sesión
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); // Ruta para mostrar el formulario de inicio de sesión
    Route::get('/login', [AuthController::class, 'showLoginForm']); // Ruta para mostrar el formulario de inicio de sesión
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt'); // Ruta para procesar el inicio de sesión

    // Ruta para recuperación de contraseña
    Route::get('/forgot-password', function () {
        return view('public.recover-password');
    })->name('password.request');
});

// Rutas protegidas (sólo usuarios con sesión activa)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Ruta para cerrar sesión

    // Panel Recepcionista
    Route::get('/recepcion', function () {
        return 'Panel de Recepción - Bienvenido, ' . auth()->user()->name;
    })->name('receptionist.home');

    // Panel Técnico
    Route::get('/tecnico', function () {
        return 'Panel del Taller / Técnico - Bienvenido, ' . auth()->user()->name;
    })->name('technician.home');
});

// Rutas protegidas para Administrador (sólo con sesión activa)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Rutas para el módulo de home-dashboard
    // Vista principal del panel de administración (dashboard)
    Route::get('/', [AdminController::class, 'home']);
    Route::get('/home', [AdminController::class, 'home'])->name('home');

    // Rutas para el módulo de gestión de usuarios
    // Vista principal del módulo de gestión de usuarios
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users.index');

    // Listar usuarios
    Route::get('/users/list', [AdminController::class, 'showUsers'])->name('users.list');

    // Crear nuevo usuario
    Route::post('/users/create', [AdminController::class, 'storeUser'])->name('users.create');

    // Editar usuario existente
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');

    // Cambiar contraseña de usuario
    Route::patch('/users/{id}/password', [AdminController::class, 'updateUserPassword'])->name('users.update-password');

    // Activar / Desactivar usuario
    Route::patch('/users/{id}/toggle-state', [AdminController::class, 'toggleUserState'])->name('users.toggle-state');
});

// Ruta pública de error 404
Route::fallback(function () {
    return response()->view('public.not-found', [], 404);
});