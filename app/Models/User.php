<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


public function ingresosAsignados()
{
    return $this->hasMany(Ingreso::class, 'mecanico_id');
}

public function solicitudesRepuestos()
{
    return $this->hasMany(
        SolicitudRepuesto::class,
        'mecanico_id'
    );
}
}
