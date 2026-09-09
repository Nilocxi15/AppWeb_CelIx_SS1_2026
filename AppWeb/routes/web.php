<?php

use Illuminate\Support\Facades\Route;

/*
* RUTAS PÚBLICAS
*/
// Ruta para login
Route::get('/', function () {
    return view('login');
});

// Ruta para recuperación de contraseña
Route::get('/forgot-password', function () {
    return view('public.recover-password');
});

// Ruta para error 404 NOT FOUND
Route::fallback(function () {
    return view('public.not-found');
});