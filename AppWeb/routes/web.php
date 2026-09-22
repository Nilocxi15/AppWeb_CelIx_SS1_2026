<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TicketTrackingController;
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

// Rutas públicas de Seguimiento de Reparaciones mediante Código QR (sin restricción de sesión)
Route::get('/seguimiento/{token}', [TicketTrackingController::class, 'show'])->name('tickets.tracking');
Route::get('/seguimiento/{token}/pdf', [TicketTrackingController::class, 'downloadPdf'])->name('tickets.tracking.pdf');

// Rutas protegidas (sólo usuarios con sesión activa)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Ruta para cerrar sesión

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

    // Rutas para el módulo de configuración del sistema
    Route::get('/configuracion', [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/configuracion/tipos-dispositivos', [AdminController::class, 'storeDeviceType'])->name('settings.device-types.store');
    Route::put('/configuracion/tipos-dispositivos/{id}', [AdminController::class, 'updateDeviceType'])->name('settings.device-types.update');
    Route::patch('/configuracion/tipos-dispositivos/{id}/toggle-status', [AdminController::class, 'toggleDeviceTypeStatus'])->name('settings.device-types.toggle-status');
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

    // Registrar recepción de dispositivo (Ticket de servicio)
    Route::post('/reception/dispositivos', [ReceptionistController::class, 'storeDeviceIntake'])->name('devices.store');

    // Descarga de ticket en formato PDF (etiqueta y comprobante)
    Route::get('/tickets/{id}/pdf', [ReceptionistController::class, 'downloadTicketPdf'])->name('tickets.pdf');

    /**
     * Rutas para el módulo de Inventario
     */
    // Vista principal del módulo de inventario
    Route::get('/reception/inventory', [ReceptionistController::class, 'inventory'])->name('inventory');

    // Guardar producto
    Route::post('/reception/inventory/products', [ReceptionistController::class, 'storeProduct'])->name('inventory.products.store');

    // Actualizar producto y cambiar estado de producto
    Route::put('/reception/inventory/products/{barcode}', [ReceptionistController::class, 'updateProduct'])->name('inventory.products.update');

    // Cambiar estado de producto (activo/inactivo)
    Route::patch('/reception/inventory/products/{barcode}/toggle-status', [ReceptionistController::class, 'toggleProductStatus'])->name('inventory.products.toggle-status');

    // Guardar categoría de producto
    Route::post('/reception/inventory/categorias', [ReceptionistController::class, 'storeCategory'])->name('inventory.categories.store');

    // Actualizar categoría de producto
    Route::put('/reception/inventory/categorias/{id}', [ReceptionistController::class, 'updateCategory'])->name('inventory.categories.update');

    // Cambiar estado de categoría de producto (activo/inactivo)
    Route::patch('/reception/inventory/categorias/{id}/toggle-status', [ReceptionistController::class, 'toggleCategoryStatus'])->name('inventory.categories.toggle-status');

    // Guardar movimiento de inventario (entrada/salida)
    Route::post('/reception/inventory/movimientos', [ReceptionistController::class, 'storeInventoryMovement'])->name('inventory.movements.store');

    // Módulo de Historiales (Kardex independiente)
    Route::get('/reception/historiales', [ReceptionistController::class, 'kardex'])->name('kardex');

    // Módulo de Entregas de Dispositivos (Tickets Finalizados)
    Route::get('/reception/entregas', [ReceptionistController::class, 'deliveries'])->name('deliveries');

    // Procesar entrega de ticket
    Route::post('/reception/entregas/{id}/entregar', [ReceptionistController::class, 'deliverTicket'])->name('deliveries.process');
});

// Rutas protegidas para Técnico (sólo con sesión activa)
Route::middleware(['auth', 'role:ADMINISTRADOR,TECNICO'])->prefix('technician')->name('technician.')->group(function () {
    /**
     * Rutas para el módulo de la página de inicio
     */
    // Vista principal del panel de técnico
    Route::get('/technician', [TechnicianController::class, 'home'])->name('home');
    // Actualizar estado de ticket
    Route::patch('/tickets/{id}/state', [TechnicianController::class, 'updateState'])->name('tickets.state');
    // Guardar nota de ticket
    Route::post('/tickets/{id}/notes', [TechnicianController::class, 'storeNote'])->name('tickets.notes.store');
    // Listar notas de ticket
    Route::get('/tickets/{id}/notes', [TechnicianController::class, 'getNotes'])->name('tickets.notes.list');
});

// Ruta pública de error 404
Route::fallback(function () {
    return response()->view('public.not-found', [], 404);
});