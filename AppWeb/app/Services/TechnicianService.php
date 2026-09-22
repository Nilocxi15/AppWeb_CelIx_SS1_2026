<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TechnicianService
{
    /**
     * Jerarquía estricta de avance de estados técnicos (secuencia unidireccional e irreversible).
     */
    protected const STATE_ORDER = [
        'Recibido'    => 1,
        'Diagnóstico' => 2,
        'Reparación'  => 3,
        'Finalizado'  => 4,
        'Entregado'   => 5,
    ];

    /**
     * Obtener los tickets de trabajo con paginación y filtros.
     * - Si el usuario es TECNICO: únicamente sus tickets asignados.
     * - Si el usuario es ADMINISTRADOR: todos los tickets, con opción a filtrar por técnico.
     */
    public function getTicketsPaginated(User $user, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Ticket::with(['device.client', 'device.deviceType', 'technician', 'receptionist', 'notes.user']);

        // Control estricto de acceso por rol
        if (!$user->hasRole('ADMINISTRADOR')) {
            $query->where('id_user_technician', $user->id);
        } elseif (!empty($filters['technician_id'])) {
            $query->where('id_user_technician', $filters['technician_id']);
        }

        // Filtro por término de búsqueda (Folio, cliente, teléfono, marca, modelo, serie)
        if (!empty($filters['search'])) {
            $search = mb_strtolower(trim($filters['search']));
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', (int) $search);
                }
                $q->orWhereHas('device.client', function ($clientQ) use ($search) {
                    $clientQ->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
                })
                ->orWhereHas('device', function ($deviceQ) use ($search) {
                    $deviceQ->whereRaw('LOWER(brand) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(model) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(serial_number) LIKE ?', ["%{$search}%"]);
                })
                ->orWhereRaw('LOWER(reported_issue) LIKE ?', ["%{$search}%"]);
            });
        }

        // Filtro por estado
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // Filtro por fecha
        if (!empty($filters['date_from'])) {
            $query->whereDate('intake_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('intake_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Métricas rápidas (KPIs) del taller.
     */
    public function getTechnicianKPIs(User $user): array
    {
        $baseQuery = Ticket::query();

        if (!$user->hasRole('ADMINISTRADOR')) {
            $baseQuery->where('id_user_technician', $user->id);
        }

        return [
            'total_active'   => (clone $baseQuery)->whereNotIn('state', ['Entregado'])->count(),
            'in_diagnostic'  => (clone $baseQuery)->where('state', 'Diagnóstico')->count(),
            'in_repair'      => (clone $baseQuery)->where('state', 'Reparación')->count(),
            'completed'      => (clone $baseQuery)->where('state', 'Finalizado')->count(),
            'delivered'      => (clone $baseQuery)->where('state', 'Entregado')->count(),
        ];
    }

    /**
     * Obtener los estados válidos hacia los que puede avanzar un ticket.
     * Respeta la regla de que una vez avanzado el estado, NO debe poder retroceder.
     */
    public function getNextAllowedStates(string $currentState): array
    {
        $currentWeight = self::STATE_ORDER[$currentState] ?? 0;
        $allowed = [];

        foreach (self::STATE_ORDER as $state => $weight) {
            // El técnico solo puede avanzar hacia Diagnóstico, Reparación o Finalizado
            if ($weight > $currentWeight && in_array($state, ['Diagnóstico', 'Reparación', 'Finalizado'])) {
                $allowed[] = $state;
            }
        }

        return $allowed;
    }

    /**
     * Actualizar el estado de un ticket y su diagnóstico técnico.
     * Valida permisos de usuario y la regla de no retroceso de estado.
     *
     * @throws ValidationException
     */
    public function updateTicketState(int $ticketId, string $newState, ?string $diagnosis, User $user): Ticket
    {
        $ticket = Ticket::with(['device.client', 'device.deviceType', 'technician'])->findOrFail($ticketId);

        // Control de permisos
        if (!$user->hasRole('ADMINISTRADOR') && (int) $ticket->id_user_technician !== (int) $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('No tienes autorización para modificar este ticket de trabajo.');
        }

        $currentState = $ticket->state;

        // No se permite modificar tickets ya entregados por recepción
        if ($currentState === 'Entregado') {
            throw ValidationException::withMessages([
                'state' => 'El ticket ya ha sido entregado al cliente y su ciclo técnico está cerrado.',
            ]);
        }

        $currentWeight = self::STATE_ORDER[$currentState] ?? 0;
        $newWeight = self::STATE_ORDER[$newState] ?? 0;

        // Regla: una vez avanzado el estado, NO debe poder retroceder
        if ($newWeight <= $currentWeight) {
            throw ValidationException::withMessages([
                'state' => "El ticket se encuentra en estado '{$currentState}'. El flujo técnico es irreversible y no se permite retroceder a '{$newState}'.",
            ]);
        }

        // El técnico no puede marcar directamente como 'Entregado' (labor exclusiva del recepcionista)
        if ($newState === 'Entregado') {
            throw ValidationException::withMessages([
                'state' => "El estado 'Entregado' únicamente puede ser asignado por Recepción al momento de liquidar y entregar el equipo.",
            ]);
        }

        $ticket->state = $newState;

        if ($diagnosis !== null) {
            $ticket->technical_diagnosis = trim($diagnosis);
        }

        $ticket->save();

        // Registrar nota automática de cambio de estado en la bitácora
        TicketNote::create([
            'id_ticket'     => $ticket->id,
            'id_user'       => $user->id,
            'note'          => "Estado avanzado a: {$newState}" . ($diagnosis ? " | Diagnóstico: {$diagnosis}" : ""),
            'creation_date' => now(),
        ]);

        return $ticket->fresh(['device.client', 'device.deviceType', 'notes.user']);
    }

    /**
     * Agregar una nota técnica de seguimiento al ticket.
     *
     * @throws ValidationException
     */
    public function addTicketNote(int $ticketId, string $noteText, User $user): TicketNote
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Control de permisos
        if (!$user->hasRole('ADMINISTRADOR') && (int) $ticket->id_user_technician !== (int) $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('No tienes autorización para agregar notas en este ticket de trabajo.');
        }

        $note = TicketNote::create([
            'id_ticket'     => $ticket->id,
            'id_user'       => $user->id,
            'note'          => trim($noteText),
            'creation_date' => now(),
        ]);

        return $note->load('user');
    }

    /**
     * Obtener el listado de notas de un ticket ordenadas cronológicamente.
     */
    public function getTicketNotes(int $ticketId, User $user): Collection
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Control de permisos
        if (!$user->hasRole('ADMINISTRADOR') && (int) $ticket->id_user_technician !== (int) $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('No tienes autorización para consultar las notas de este ticket.');
        }

        return TicketNote::where('id_ticket', $ticketId)
            ->with('user.role')
            ->orderBy('creation_date', 'desc')
            ->get();
    }

    /**
     * Obtener la lista de todos los técnicos y administradores activos para filtros de administrador.
     */
    public function getAllActiveTechnicians(): Collection
    {
        return User::whereHas('role', function ($q) {
            $q->whereIn(DB::raw('UPPER(name)'), ['TECNICO', 'TÉCNICO', 'ADMINISTRADOR']);
        })->where('state', true)->orderBy('name', 'asc')->get();
    }
}
