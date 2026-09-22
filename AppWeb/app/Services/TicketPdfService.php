<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketPdfService
{
    /**
     * Genera la representación SVG del código QR para una URL dada.
     *
     * @param string $url URL a codificar en el QR
     * @param int $size Tamaño en píxeles (ancho y alto)
     * @return string Código SVG generado
     */
    public function generateQrCodeSvg(string $url, int $size = 140): string
    {
        return (string) QrCode::size($size)
            ->color(23, 23, 23)
            ->margin(1)
            ->generate($url);
    }

    /**
     * Genera el Data URI en formato Base64 para incrustar el código QR en imágenes y PDFs.
     *
     * @param string $url URL a codificar en el QR
     * @param int $size Tamaño en píxeles
     * @return string Cadena Data URI ("data:image/svg+xml;base64,...")
     */
    public function generateQrCodeDataUri(string $url, int $size = 140): string
    {
        $svg = $this->generateQrCodeSvg($url, $size);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Construye la instancia del documento PDF para el ticket especificado,
     * conteniendo la etiqueta física de taller para el equipo y el comprobante
     * del cliente con el código QR de seguimiento en tiempo real.
     *
     * @param Ticket $ticket
     * @return DomPdfWrapper
     */
    public function buildTicketPdf(Ticket $ticket): DomPdfWrapper
    {
        // Cargar relaciones requeridas si aún no están cargadas
        $ticket->loadMissing(['device.client', 'device.deviceType', 'technician', 'receptionist']);

        $trackingUrl = route('tickets.tracking', $ticket->qr_token);
        $qrDataUriSmall = $this->generateQrCodeDataUri($trackingUrl, 110);
        $qrDataUriLarge = $this->generateQrCodeDataUri($trackingUrl, 160);

        return Pdf::loadView('pdf.ticket', [
            'ticket'          => $ticket,
            'device'          => $ticket->device,
            'client'          => $ticket->device->client,
            'deviceType'      => $ticket->device->deviceType,
            'technician'      => $ticket->technician,
            'receptionist'    => $ticket->receptionist,
            'trackingUrl'     => $trackingUrl,
            'qrDataUriSmall'  => $qrDataUriSmall,
            'qrDataUriLarge'  => $qrDataUriLarge,
        ])->setPaper('letter', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);
    }
}
