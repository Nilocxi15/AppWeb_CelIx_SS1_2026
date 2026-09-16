<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function __construct(protected AdminService $adminService)
    {}

    // Vista del panel de administración (dashboard)
    public function home()
    {
        return view('admin.dashboard');
    }

    // Vista del módulo de gestión de usuarios
    public function manageUsers(Request $request)
    {
        $filters = $request->only(['search', 'role', 'state', 'sort_by', 'sort_direction']);
        $perPage = (int) $request->input('per_page', 5);

        $users = $this->adminService->getUsersPaginated($filters, $perPage);
        $roles = $this->adminService->getAllRoles();
        $stats = $this->adminService->getUserStats();

        return view('admin.users.index', [
            'users'         => $users,
            'roles'         => $roles,
            'totalUsers'    => $stats['total'],
            'activeUsers'   => $stats['active'],
            'inactiveUsers' => $stats['inactive'],
        ]);
    }

    // Listar usuarios vía JSON (para APIs o recargas AJAX)
    public function showUsers(Request $request)
    {
        $filters = $request->only(['search', 'role', 'state', 'sort_by', 'sort_direction']);
        $perPage = (int) $request->input('per_page', 5);

        $users = $this->adminService->getUsersPaginated($filters, $perPage);

        return response()->json($users);
    }

    // Registrar nuevo usuario
    public function storeUser(StoreUserRequest $request)
    {
        $user = $this->adminService->createUser($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente.',
                'user'    => $user,
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario registrado exitosamente.');
    }

    // Actualizar usuario existente
    public function updateUser(UpdateUserRequest $request, $id)
    {
        $user = $this->adminService->updateUser($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Información de usuario actualizada exitosamente.',
                'user'    => $user,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Información de usuario actualizada exitosamente.');
    }

    // Cambiar contraseña de usuario
    public function updateUserPassword(UpdateUserPasswordRequest $request, $id)
    {
        $this->adminService->updateUserPassword($id, $request->validated()['password']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Contraseña actualizada exitosamente.');
    }

    // Activar / Desactivar usuario
    public function toggleUserState(Request $request, $id)
    {
        try {
            $user = $this->adminService->toggleUserState($id, auth()->id());
            $statusText = $user->state ? 'activada' : 'desactivada';
            $message = "La cuenta de @{$user->username} ha sido {$statusText} exitosamente.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'state'   => $user->state,
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', $message);
        } catch (ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return redirect()->route('admin.users.index')->with('error', $errorMessage);
        }
    }
}
