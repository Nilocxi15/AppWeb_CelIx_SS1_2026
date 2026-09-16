<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user_receptionist',
        'sale_date',
        'total_sale',
    ];

    protected function casts(): array
    {
        return [
            'sale_date'  => 'datetime',
            'total_sale' => 'decimal:2',
        ];
    }

    /**
     * Recepcionista que realizó la venta.
     */
    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_receptionist', 'id');
    }

    /**
     * Líneas de detalle de la venta (artículos vendidos).
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'id_sale', 'id');
    }

    /**
     * Movimientos de Kardex asociados a esta venta.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'id_sale', 'id');
    }

    /**
     * Registro en el libro mayor financiero correspondiente a esta venta.
     */
    public function financialTransaction(): HasOne
    {
        return $this->hasOne(FinancialTransaction::class, 'id_sale', 'id');
    }
}
