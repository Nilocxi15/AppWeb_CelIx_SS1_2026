<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceptionistController;
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
    Route::middleware(['role:ADMINISTRADOR,RECEPCIONISTA'])->group(function () {
        Route::get('/recepcion', [ReceptionistController::class, 'home'])->name('receptionist.home');
        Route::post('/recepcion/ventas', [ReceptionistController::class, 'storeSale'])->name('receptionist.sales.store');

        // Módulo de Inventario
        Route::get('/recepcion/inventario', [ReceptionistController::class, 'inventory'])->name('receptionist.inventory');
        Route::post('/recepcion/inventario/productos', [ReceptionistController::class, 'storeProduct'])->name('receptionist.inventory.products.store');
        Route::put('/recepcion/inventario/productos/{barcode}', [ReceptionistController::class, 'updateProduct'])->name('receptionist.inventory.products.update');
        Route::patch('/recepcion/inventario/productos/{barcode}/toggle-status', [ReceptionistController::class, 'toggleProductStatus'])->name('receptionist.inventory.products.toggle-status');

        Route::post('/recepcion/inventario/categorias', [ReceptionistController::class, 'storeCategory'])->name('receptionist.inventory.categories.store');
        Route::put('/recepcion/inventario/categorias/{id}', [ReceptionistController::class, 'updateCategory'])->name('receptionist.inventory.categories.update');
        Route::patch('/recepcion/inventario/categorias/{id}/toggle-status', [ReceptionistController::class, 'toggleCategoryStatus'])->name('receptionist.inventory.categories.toggle-status');

        Route::post('/recepcion/inventario/movimientos', [ReceptionistController::class, 'storeInventoryMovement'])->name('receptionist.inventory.movements.store');

        // Módulo de Historiales (Kardex independiente)
        Route::get('/recepcion/historiales', [ReceptionistController::class, 'kardex'])->name('receptionist.kardex');
    });

    // Panel Técnico
    Route::get('/tecnico', function () {
        return 'Panel del Taller / Técnico - Bienvenido, ' . auth()->user()->name;
    })->name('technician.home');

    // Perfil de Usuario Universal (Accesible para cualquier usuario autenticado)
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Rutas protegidas para Administrador (sólo con sesión activa)
Route::middleware(['auth', 'role:ADMINISTRADOR'])->prefix('admin')->name('admin.')->group(function () {
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

// Rutas protegidas para Recepcionista (sólo con sesión activa)
Route::middleware(['auth', 'role:ADMINISTRADOR,RECEPCIONISTA'])->prefix('receptionist')->name('receptionist.')->group(function () {
    /**
     * Rutas para el módulo de la página de inicio
     */
    // Vista principal del panel de recepcionista
    Route::get('/reception', [ReceptionistController::class, 'home'])->name('home');

    // Guardar venta (multi-artículos)
    Route::post('/sales', [ReceptionistController::class, 'storeSale'])->name('sales.store');
});

// Ruta pública de error 404
Route::fallback(function () {
    return response()->view('public.not-found', [], 404);
});