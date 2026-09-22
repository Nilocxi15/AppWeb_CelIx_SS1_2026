<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TicketTrackingController extends Controller
{
    /**
     * Muestra la página pública de seguimiento en tiempo real del estado del ticket
     * escaneado a través del código QR. No requiere autenticación.
     *
     * @param string $token Token UUID del ticket
     * @param TicketPdfService $pdfService
     * @return View|Response
     */
    public function show(string $token, TicketPdfService $pdfService): View|Response
    {
        if (!Str::isUuid($token)) {
            return response()->view('public.not-found', [], 404);
        }

        $ticket = Ticket::where('qr_token', $token)
            ->with(['device.client', 'device.deviceType', 'technician', 'receptionist', 'notes.user'])
            ->first();

        if (!$ticket) {
            return response()->view('public.not-found', [], 404);
        }

        // Definición de las etapas del ciclo de reparación CelIx
        $stages = [
            [
                'key'         => 'Recibido',
                'title'       => '1. Recibido',
                'icon'        => 'bi-box-arrow-in-down',
                'description' => 'Equipo recibido e inventariado en recepción.',
            ],
            [
                'key'         => 'Diagnóstico',
                'title'       => '2. En Diagnóstico',
                'icon'        => 'bi-search',
                'description' => 'Revisión técnica de componentes y verificación de fallas.',
            ],
            [
                'key'         => 'Reparación',
                'title'       => '3. En Reparación',
                'icon'        => 'bi-tools',
                'description' => 'Trabajo de reparación y cambio de repuestos en proceso.',
            ],
            [
                'key'         => 'Finalizado',
                'title'       => '4. Finalizado',
                'icon'        => 'bi-check2-circle',
                'description' => 'Pruebas de calidad aprobadas. Listo para entrega en recepción.',
            ],
            [
                'key'         => 'Entregado',
                'title'       => '5. Entregado',
                'icon'        => 'bi-hand-thumbs-up',
                'description' => 'Dispositivo devuelto al cliente y liquidado.',
            ],
        ];

        // Determinar índice actual en el stepper
        $stateOrder = ['Recibido' => 0, 'Diagnóstico' => 1, 'Reparación' => 2, 'Finalizado' => 3, 'Entregado' => 4];
        $currentStepIndex = $stateOrder[$ticket->state] ?? 0;

        $trackingUrl = route('tickets.tracking', $ticket->qr_token);
        $qrSvg = $pdfService->generateQrCodeSvg($trackingUrl, 160);

        return view('public.ticket-tracking', compact(
            'ticket',
            'stages',
            'currentStepIndex',
            'trackingUrl',
            'qrSvg'
        ));
    }

    /**
     * Descarga pública del comprobante en formato PDF asociado al código QR.
     *
     * @param string $token
     * @param TicketPdfService $pdfService
     * @return SymfonyResponse
     */
    public function downloadPdf(string $token, TicketPdfService $pdfService): SymfonyResponse
    {
        if (!Str::isUuid($token)) {
            return response()->view('public.not-found', [], 404);
        }

        $ticket = Ticket::where('qr_token', $token)->first();

        if (!$ticket) {
            return response()->view('public.not-found', [], 404);
        }

        $pdf = $pdfService->buildTicketPdf($ticket);

        return $pdf->download("ticket-celix-TK-{$ticket->id}.pdf");
    }
}
