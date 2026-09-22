<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'dpi',
    ];

    /**
     * Dispositivos registrados a nombre del cliente.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'id_client', 'id');
    }
}
