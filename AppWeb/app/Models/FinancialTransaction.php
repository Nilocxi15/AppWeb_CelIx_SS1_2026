<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    protected $table = 'financial_transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_sale',
        'id_ticket',
        'amount',
        'type',
        'concept',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date'   => 'datetime',
        ];
    }

    /**
     * Venta que originó el ingreso financiero (si aplica).
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'id_sale', 'id');
    }
}
