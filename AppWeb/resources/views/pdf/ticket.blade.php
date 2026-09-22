<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket de Servicio #TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }} | CelIx</title>
    <style>
        @page {
            margin: 20px 25px;
            size: letter portrait;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #171717;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Utilidades generales */
        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6B7280;
        }

        .text-primary {
            color: #ED1C24;
        }

        .text-dark {
            color: #171717;
        }

        .uppercase {
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* SECCIÓN 1: ETIQUETA FÍSICA PARA PEGAR EN EL DISPOSITIVO */
        .sticker-card {
            border: 2px dashed #ED1C24;
            border-radius: 6px;
            padding: 10px 14px;
            background-color: #FAFAFA;
            margin-bottom: 12px;
        }

        .sticker-badge {
            display: inline-block;
            background-color: #ED1C24;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .cut-divider {
            border: none;
            border-top: 1px dashed #9CA3AF;
            color: #6B7280;
            text-align: center;
            height: 14px;
            margin: 12px 0 16px 0;
            position: relative;
        }

        .cut-divider::after {
            content: "✂ CORTAR AQUÍ  - - -  ETIQUETA PARA EL DISPOSITIVO (ARRIBA)  /  COMPROBANTE DEL CLIENTE (ABAJO) ✂";
            position: relative;
            top: -9px;
            background: #FFFFFF;
            padding: 0 10px;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* SECCIÓN 2: COMPROBANTE OFICIAL DE RECEPCIÓN (COPIA CLIENTE) */
        .receipt-header {
            border-bottom: 2px solid #ED1C24;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #171717;
            letter-spacing: -0.5px;
        }

        .brand-accent {
            color: #ED1C24;
        }

        .receipt-badge {
            background-color: #171717;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .info-box {
            border: 1px solid #E5E5E5;
            border-radius: 5px;
            background-color: #FFFFFF;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #ED1C24;
            border-bottom: 1px solid #F3F4F6;
            padding-bottom: 3px;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 10.5px;
        }

        .data-label {
            color: #6B7280;
            width: 32%;
            font-weight: 500;
        }

        .data-value {
            color: #171717;
            font-weight: bold;
            width: 68%;
        }

        /* Caja económica destacada */
        .finance-box {
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 5px;
            padding: 8px 12px;
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .finance-table td {
            padding: 2px 6px;
            font-size: 11px;
        }

        .finance-total {
            font-size: 13px;
            color: #ED1C24;
            font-weight: bold;
        }

        /* Caja de seguimiento QR */
        .tracking-box {
            border: 1px solid #ED1C24;
            background-color: #FFF5F5;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }

        .terms-text {
            font-size: 8px;
            color: #6B7280;
            line-height: 1.3;
            text-align: justify;
            margin-top: 6px;
            border-top: 1px solid #E5E5E5;
            padding-top: 5px;
        }

        .signature-table {
            margin-top: 20px;
            width: 100%;
        }

        .signature-line {
            border-top: 1px solid #9CA3AF;
            width: 80%;
            margin: 0 auto;
            padding-top: 3px;
            font-size: 9px;
            color: #6B7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- PARTE SUPERIOR: ETIQUETA ADHESIVA PARA EL DISPOSITIVO (TALLER TÉCNICO)   -->
    <div class="sticker-card">
        <table>
            <tr>
                <!-- Columna Datos Principales -->
                <td style="width: 78%; vertical-align: top;">
                    <div style="margin-bottom: 4px;">
                        <span class="sticker-badge">ETIQUETA DE TALLER</span>
                        <span style="font-size: 13px; font-weight: bold; margin-left: 6px; color: #171717;">
                            Folio #TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        <span style="font-size: 9px; color: #6B7280; margin-left: 8px;">
                            Fecha:
                            {{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <table style="width: 100%; font-size: 10px;">
                        <tr>
                            <td style="width: 20%; color: #6B7280;">Cliente:</td>
                            <td style="width: 80%; font-weight: bold; color: #171717;">
                                {{ $client->name ?? 'N/D' }} {{ $client->lastname ?? '' }}
                                (Tel: {{ $client->phone ?? 'Sin teléfono' }})
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #6B7280;">Equipo:</td>
                            <td style="font-weight: bold; color: #ED1C24;">
                                {{ $device->brand ?? '' }} {{ $device->model ?? '' }}
                                <span style="color: #6B7280; font-weight: normal;">
                                    [{{ $deviceType->name ?? 'Dispositivo' }}]
                                    @if(!empty($device->serial_number)) - S/N: {{ $device->serial_number }} @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #6B7280;">PIN / Clave:</td>
                            <td style="font-weight: bold; color: #171717;">
                                {{ $ticket->device_password ?: 'Sin contraseña / Desbloqueado' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #6B7280;">Falla:</td>
                            <td style="color: #171717;">
                                {{ \Illuminate\Support\Str::limit($ticket->reported_issue, 85) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #6B7280;">Especialista:</td>
                            <td style="color: #171717; font-weight: 500;">
                                {{ $technician ? ($technician->name . ' ' . $technician->lastname) : 'Por Asignar' }}
                                &nbsp;|&nbsp;
                                <span style="color: #6B7280;">Recepción:</span>
                                {{ $receptionist ? ($receptionist->name . ' ' . $receptionist->lastname) : 'Mostrador' }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Columna QR para Pegar en el Dispositivo -->
                <td style="width: 22%; text-align: center; vertical-align: middle; padding-left: 8px;">
                    <img src="{{ $qrDataUriSmall }}" alt="QR Seguimiento"
                        style="width: 85px; height: 85px; display: block; margin: 0 auto;">
                    <span style="display: block; font-size: 7.5px; font-weight: bold; color: #ED1C24; margin-top: 3px;">
                        ESCANEAR TICKET
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Línea divisoria de corte -->
    <div class="cut-divider"></div>

    <!-- PARTE INFERIOR: COMPROBANTE OFICIAL DE SERVICIO (COPIA PARA EL CLIENTE)  -->
    <div class="receipt-header">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="brand-title">Cel<span class="brand-accent">Ix</span></div>
                    <div style="font-size: 9.5px; color: #4B5563;">
                        Centro Especializado en Reparación y Soporte Tecnológico<br>
                        Teléfono / WhatsApp: **** - **** &bull; iservicesanmarcos@gmail.com<br>
                        Guatemala, C.A.
                    </div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <div class="receipt-badge">ORDEN DE SERVICIO</div>
                    <div style="font-size: 14px; font-weight: bold; color: #ED1C24; margin-top: 4px;">
                        #TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="font-size: 9px; color: #6B7280;">
                        Ingreso:
                        {{ $ticket->intake_date ? $ticket->intake_date->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tablas de Datos en 2 Columnas -->
    <table>
        <tr>
            <!-- Columna Izquierda: Datos del Cliente y Dispositivo -->
            <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                <div class="info-box">
                    <div class="info-box-title">Información del Cliente</div>
                    <table class="data-table">
                        <tr>
                            <td class="data-label">Cliente:</td>
                            <td class="data-value">{{ $client->name ?? 'Consumidor' }}
                                {{ $client->lastname ?? 'Final' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="data-label">Teléfono:</td>
                            <td class="data-value">{{ $client->phone ?? 'Sin teléfono registrado' }}</td>
                        </tr>
                        @if(!empty($client->dpi))
                            <tr>
                                <td class="data-label">DPI / CUI:</td>
                                <td class="data-value">{{ $client->dpi }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="info-box">
                    <div class="info-box-title">Datos del Dispositivo</div>
                    <table class="data-table">
                        <tr>
                            <td class="data-label">Dispositivo:</td>
                            <td class="data-value">{{ $deviceType->name ?? 'Equipo' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Marca / Modelo:</td>
                            <td class="data-value text-primary">{{ $device->brand ?? '' }} {{ $device->model ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="data-label">Serie / IMEI:</td>
                            <td class="data-value">{{ $device->serial_number ?: 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Desbloqueo:</td>
                            <td class="data-value">{{ $ticket->device_password ?: 'Sin contraseña' }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- Columna Derecha: Recepción Técnica y Liquidación Económica -->
            <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                <div class="info-box">
                    <div class="info-box-title">Recepción Técnica de Taller</div>
                    <table class="data-table">
                        <tr>
                            <td class="data-label">Falla Reportada:</td>
                            <td class="data-value" style="font-weight: normal; color: #171717;">
                                {{ $ticket->reported_issue }}
                            </td>
                        </tr>
                        @if(!empty($ticket->reception_notes))
                            <tr>
                                <td class="data-label">Observaciones:</td>
                                <td class="data-value" style="font-weight: normal; color: #4B5563;">
                                    {{ $ticket->reception_notes }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="data-label">Estado Inicial:</td>
                            <td class="data-value" style="color: #16A34A;">{{ $ticket->state ?? 'Recibido' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Técnico Asignado:</td>
                            <td class="data-value">
                                {{ $technician ? ($technician->name . ' ' . $technician->lastname) : 'Por Asignar' }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Apartado Económico -->
                <div class="finance-box">
                    <table class="finance-table" style="width: 100%;">
                        <tr>
                            <td class="text-muted">Costo del Trabajo (Estimado):</td>
                            <td class="text-end fw-bold">Q {{ number_format((float) $ticket->total_charged, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Anticipo Abonado:</td>
                            <td class="text-end fw-bold text-dark" style="color: #16A34A;">- Q
                                {{ number_format((float) $ticket->deposit, 2) }}
                            </td>
                        </tr>
                        <tr style="border-top: 1px solid #FCA5A5;">
                            <td class="fw-bold text-dark" style="padding-top: 4px;">Saldo Restante a Liquidar:</td>
                            <td class="text-end finance-total" style="padding-top: 4px;">Q
                                {{ number_format((float) $ticket->remaining_balance, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Caja de Seguimiento en Tiempo Real mediante Código QR -->
    <div class="tracking-box">
        <table>
            <tr>
                <td style="width: 25%; text-align: center; vertical-align: middle;">
                    <img src="{{ $qrDataUriLarge }}" alt="Código QR de Seguimiento"
                        style="width: 105px; height: 105px; display: block; margin: 0 auto;">
                </td>
                <td style="width: 75%; vertical-align: middle; padding-left: 14px;">
                    <div style="font-size: 12px; font-weight: bold; color: #ED1C24; margin-bottom: 3px;">
                        CONSULTA EL ESTADO DE TU EQUIPO EN TIEMPO REAL
                    </div>
                    <div style="font-size: 9.5px; color: #374151; margin-bottom: 5px;">
                        Escanea el código QR con la cámara de tu teléfono para conocer en cualquier momento si tu
                        dispositivo está en diagnóstico, en reparación o listo para ser entregado.
                    </div>
                    <div style="font-size: 8.5px; color: #6B7280; font-family: monospace;">
                        Enlace web directo: <span style="color: #171717; font-weight: bold;">{{ $trackingUrl }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Términos y Condiciones Legales del Taller -->
    <div class="terms-text">
        <strong>TÉRMINOS Y CONDICIONES DE SERVICIO:</strong> 1. Es requisito indispensable presentar este comprobante
        físico o digital para retirar el equipo. 2. CelIx no se hace responsable por pérdida de datos o información
        almacenada en el dispositivo; se recomienda realizar respaldo previo. 3. Los equipos no reclamados después de 60
        días naturales a partir de la fecha de ingreso causarán recargo de almacenaje y podrán ser dispuestos para
        recuperar costos según la ley aplicable. 4. Las reparaciones cuentan con garantía técnica sobre el trabajo
        realizado (no cubre golpes, humedad o manipulación de terceros).
    </div>

    <!-- Firmas -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    Firma Recepción / Taller CelIx<br>
                    <span style="font-size: 8px; color: #9CA3AF;">Atendido por:
                        {{ $receptionist ? ($receptionist->name . ' ' . $receptionist->lastname) : 'Personal CelIx' }}</span>
                </div>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    Firma de Conformidad del Cliente<br>
                    <span style="font-size: 8px; color: #9CA3AF;">{{ $client->name ?? 'Cliente' }}
                        {{ $client->lastname ?? '' }}</span>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>