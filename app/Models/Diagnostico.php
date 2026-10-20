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

    // Ingreso al que pertenece el diagnóstico
    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class);
    }

    // Reparación asociada al diagnóstico
    public function reparacion()
    {
        return $this->hasOne(Reparacion::class);
    }
}
