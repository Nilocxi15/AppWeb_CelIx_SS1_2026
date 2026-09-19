<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'bar_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'bar_code',
        'id_category',
        'name',
        'description',
        'stock',
        'minium_stock',
        'price',
        'status',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'stock'        => 'integer',
            'minium_stock' => 'integer',
            'price'        => 'decimal:2',
            'status'       => 'boolean',
        ];
    }

    /**
     * Categoría a la que pertenece el producto.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryProduct::class, 'id_category', 'id');
    }

    /**
     * Detalles de venta en los que aparece este producto.
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'product_bar_code', 'bar_code');
    }

    /**
     * Movimientos de inventario (Kardex) asociados a este producto.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_bar_code', 'bar_code');
    }
}
