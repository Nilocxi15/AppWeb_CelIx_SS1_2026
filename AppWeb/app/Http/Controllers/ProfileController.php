<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Muestra la vista del perfil del usuario autenticado.
     */
    public function show(): View
    {
        $user = auth()->user();
        $roleName = strtoupper(trim($user->role->name ?? ''));
        $layout = match ($roleName) {
            'RECEPCIONISTA' => 'layouts.receptionist',
            default         => 'layouts.admin',
        };

        return view('profile.show', compact('user', 'layout'));
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     */
    public function updatePassword(UpdateProfilePasswordRequest $request): JsonResponse|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->validated()['new_password']),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '¡Contraseña modificada exitosamente!',
            ]);
        }

        return redirect()->route('profile.show')->with('success', '¡Contraseña modificada exitosamente!');
    }
}
