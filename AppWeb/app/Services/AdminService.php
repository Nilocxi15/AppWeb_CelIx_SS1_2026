<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminService
{
    /**
     * Obtener todos los usuarios registrados en la base de datos aplicando filtros de búsqueda y paginación.
     *
     * @param array $filters Filtros de búsqueda (search, role, state)
     * @param int $perPage Cantidad explícita de registros por página
     * @return LengthAwarePaginator
     */
    public function getUsersPaginated(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = User::with('role');

        // Búsqueda por texto (nombre, apellido, username o email) de forma insensible a mayúsculas
        if (!empty($filters['search'])) {
            $search = mb_strtolower(trim($filters['search']));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        // Filtro por rol
        if (!empty($filters['role'])) {
            $query->where('id_rol', $filters['role']);
        }

        // Filtro por estado (activo = '1', inactivo = '0')
        if (isset($filters['state']) && $filters['state'] !== '') {
            $query->where('state', (bool) $filters['state']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Obtener métricas y KPIs de usuarios (totales, activos e inactivos).
     *
     * @return array
     */
    public function getUserStats(): array
    {
        return [
            'total'    => User::count(),
            'active'   => User::where('state', true)->count(),
            'inactive' => User::where('state', false)->count(),
        ];
    }

    /**
     * Obtener todos los roles disponibles en el sistema ordenados alfabéticamente.
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::orderBy('name', 'asc')->get();
    }

    /**
     * Registrar un nuevo usuario en la base de datos.
     *
     * @param array $data Datos validados del usuario
     * @return User
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'id_rol'   => $data['id_rol'],
                'name'     => trim($data['name']),
                'lastname' => trim($data['lastname']),
                'username' => trim($data['username']),
                'email'    => trim($data['email']),
                'password' => Hash::make($data['password']),
                'state'    => isset($data['state']) ? (bool) $data['state'] : true,
            ]);
        });
    }

    /**
     * Actualizar los datos de perfil de un usuario existente.
     *
     * @param int|string $id Identificador del usuario
     * @param array $data Datos validados para actualizar
     * @return User
     */
    public function updateUser(int|string $id, array $data): User
    {
        $user = User::findOrFail($id);

        $user->update([
            'name'     => trim($data['name']),
            'lastname' => trim($data['lastname']),
            'id_rol'   => $data['id_rol'],
            'state'    => isset($data['state']) ? (bool) $data['state'] : $user->state,
        ]);

        return $user;
    }

    /**
     * Actualizar la contraseña de un usuario encriptada con Hash::make.
     *
     * @param int|string $id Identificador del usuario
     * @param string $newPassword Nueva contraseña en texto plano
     * @return User
     */
    public function updateUserPassword(int|string $id, string $newPassword): User
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $user;
    }

    /**
     * Alternar el estado (activo/inactivo) de un usuario.
     * Impide que un administrador desactive su propia cuenta activa.
     *
     * @param int|string $id Identificador del usuario a modificar
     * @param int|string $currentUserId Identificador del usuario autenticado que realiza la acción
     * @return User
     * @throws ValidationException
     */
    public function toggleUserState(int|string $id, int|string $currentUserId): User
    {
        if ((int) $id === (int) $currentUserId) {
            throw ValidationException::withMessages([
                'user' => 'No puedes desactivar tu propia cuenta de administrador.',
            ]);
        }

        $user = User::findOrFail($id);
        $user->state = !$user->state;
        $user->save();

        return $user;
    }
}
