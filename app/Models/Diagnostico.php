<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $fillable = [
        'ingreso_id',
        'descripcion_fallas',
        'reparaciones_necesarias'
    ];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }
}
