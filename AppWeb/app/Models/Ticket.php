<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'id_user_receptionist',
        'id_device',
        'id_user_technician',
        'state',
        'reported_issue',
        'device_password',
        'reception_notes',
        'technical_diagnosis',
        'total_charged',
        'deposit',
        'qr_token',
        'intake_date',
        'return_date',
    ];

    protected function casts(): array
    {
        return [
            'total_charged'     => 'decimal:2',
            'deposit'           => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'intake_date'       => 'datetime',
            'return_date'       => 'datetime',
        ];
    }

    /**
     * Usuario recepcionista que registró el ticket.
     */
    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_receptionist');
    }

    /**
     * Dispositivo asociado a este ticket.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'id_device');
    }

    /**
     * Técnico asignado para el servicio.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_technician');
    }

    /**
     * Transacciones financieras vinculadas al ticket (anticipos, liquidaciones).
     */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'id_ticket');
    }

    /**
     * Notas técnicas asociadas a la reparación de este ticket.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(TicketNote::class, 'id_ticket', 'id');
    }
}
