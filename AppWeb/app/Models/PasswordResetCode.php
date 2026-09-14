<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetCode extends Model
{
    // Nombre de tabla
    protected $table = 'password_reset_codes';

    // Llave primaria
    protected $primaryKey = 'id';

    // Atributos
    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'created_at',
        'updated_at',
        'is_used',
    ];

    // Casteo de tipos de dato
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    // Llave foránea
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Helper para comprobar si el código sigue vigente
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}
