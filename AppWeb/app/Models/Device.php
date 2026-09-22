<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        'id_client',
        'id_device_type',
        'serial_number',
        'brand',
        'model',
    ];

    /**
     * Cliente propietario del dispositivo.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    /**
     * Tipo de equipo / categoría del dispositivo.
     */
    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class, 'id_device_type');
    }

    /**
     * Historial de tickets de servicio de este dispositivo.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'id_device');
    }
}
