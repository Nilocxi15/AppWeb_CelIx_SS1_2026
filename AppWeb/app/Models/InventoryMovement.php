<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_bar_code',
        'id_user',
        'id_sale',
        'quantity',
        'movement_type',
        'reason',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'date'     => 'datetime',
        ];
    }

    /**
     * Producto involucrado en el movimiento de inventario (Kardex).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_bar_code', 'bar_code');
    }

    /**
     * Usuario que generó o autorizó el movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Venta asociada al movimiento de salida (si aplica).
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'id_sale', 'id');
    }
}
