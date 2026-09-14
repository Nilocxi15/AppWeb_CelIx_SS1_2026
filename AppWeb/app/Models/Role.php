<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Nombre de tabla
    protected $table = 'roles';

    // Llave primaria
    protected $primaryKey = 'id_rol';

    // Atributos
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    // Casteo de tipos de dato
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Un rol puede tener muchos usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_rol', 'id_rol');
    }
}
