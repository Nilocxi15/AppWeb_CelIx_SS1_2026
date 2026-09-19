<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryProduct extends Model
{
    protected $table = 'category_products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Productos que pertenecen a esta categoría.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_category', 'id');
    }
}
