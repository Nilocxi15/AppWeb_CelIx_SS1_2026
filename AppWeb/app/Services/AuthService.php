<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthService
{
    /**
     * Autentica a un usuario verificando credenciales y estado activo.
     * 
     * @param string $username Nombre de usuario del usuario.
     * @param string $password Contraseña del usuario.
     * @throws ValidationException
     */
    public function authenticate(string $username, string $password, bool $remember = false): void
    {
        $user = User::where('username', $username)->first();

        // Validar si el usuario existe
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['Nombre de usuario incorrecto o no registrado.'],
            ]);
        }

        // Valider si la contraseña es correcta
        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Contraseña incorrecta.'],
            ]);
        }

        // Validar si el usuario está activo
        if (!$user->state) {
            throw ValidationException::withMessages([
                'username' => ['Esta cuenta se encuentra desactivada. Contacta al administrador del sistema.'],
            ]);
        }

        Auth::login($user, $remember);

    }

    /**
     * Cierra la sesión activa en el sistema de autenticación
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Redireccionamiento después de iniciar sesión según el rol del usuario
     */
    public function redirectToDashboard(): string
    {
        $user = Auth::user();

        return match ($user->role?->name) {
            'ADMINISTRADOR' => route('admin.home'),
            'RECEPCIONISTA' => route('receptionist.home'),
            'TECNICO' => route('technician.home'),
            default => route('login'), // Redirige al login si el rol no coincide con ninguno de los anteriores
        };
    }
}