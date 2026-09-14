<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Nombre de tabla
    protected $table = 'users';

    // Llave primaria
    protected $primaryKey = 'id';

    // Atributos
    protected $fillable = [
        'id_rol',
        'name',
        'lastname',
        'username',
        'email',
        'password',
        'state',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
    ];

    // Casteo de tipos de dato
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'state' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Llave foránea
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    // Un usuario puede tener muchos códigos de restablecimiento de contraseña
    public function passwordResetCodes(): HasMany
    {
        return $this->hasMany(PasswordResetCode::class, 'email', 'email');
    }

    // Verifica si el usuario tiene un rol específico
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role?->name, $roles, true);
    }
}
