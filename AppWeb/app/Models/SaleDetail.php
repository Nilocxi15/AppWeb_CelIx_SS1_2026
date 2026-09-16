<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    protected $table = 'sale_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_sale',
        'product_bar_code',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    /**
     * Venta a la que pertenece esta línea de detalle.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'id_sale', 'id');
    }

    /**
     * Producto vendido.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_bar_code', 'bar_code');
    }
}
