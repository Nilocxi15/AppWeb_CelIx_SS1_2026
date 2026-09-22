<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketNoteRequest;
use App\Http\Requests\UpdateTicketStateRequest;
use App\Services\TechnicianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function __construct(
        protected TechnicianService $technicianService
    ) {}

    /**
     * Vista principal del panel técnico: listado de tickets asignados, métricas y filtros.
     */
    public function home(Request $request): View
    {
        $user = auth()->user();
        $filters = $request->only(['search', 'state', 'date_from', 'date_to', 'technician_id']);
        $perPage = (int) $request->input('per_page', 10);

        $tickets     = $this->technicianService->getTicketsPaginated($user, $filters, $perPage);
        $kpis        = $this->technicianService->getTechnicianKPIs($user);
        $technicians = $user->hasRole('ADMINISTRADOR')
            ? $this->technicianService->getAllActiveTechnicians()
            : collect();

        return view('technician.home', compact('tickets', 'kpis', 'technicians', 'user'));
    }

    /**
     * Actualizar el estado técnico del ticket (avance secuencial irreversible).
     */
    public function updateState(UpdateTicketStateRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $ticket = $this->technicianService->updateTicketState(
                (int) $id,
                $request->input('state'),
                $request->input('technical_diagnosis'),
                auth()->user()
            );

            $message = "¡Ticket #{$ticket->id} avanzado con éxito al estado '{$ticket->state}'!";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'ticket'  => $ticket,
                ]);
            }

            return redirect()->route('technician.home')->with('success', $message);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }
            abort(403, $e->getMessage());
        } catch (ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors'  => $e->errors(),
                ], 422);
            }

            return redirect()->route('technician.home')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Registrar una nueva nota técnica en la bitácora del ticket.
     */
    public function storeNote(StoreTicketNoteRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $note = $this->technicianService->addTicketNote(
                (int) $id,
                $request->input('note'),
                auth()->user()
            );

            $message = "Nota técnica agregada correctamente al ticket #{$id}.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'note'    => $note,
                ], 201);
            }

            return redirect()->route('technician.home')->with('success', $message);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 403);
            }
            abort(403, $e->getMessage());
        } catch (ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return redirect()->route('technician.home')->with('error', $errorMessage);
        }
    }

    /**
     * Obtener el historial completo de notas de un ticket vía JSON.
     */
    public function getNotes($id): JsonResponse
    {
        try {
            $notes = $this->technicianService->getTicketNotes((int) $id, auth()->user());

            return response()->json([
                'success' => true,
                'notes'   => $notes,
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        }
    }
}
