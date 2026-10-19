<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'color'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
}
