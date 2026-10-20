<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = [
        'vehiculo_id',
        'mecanico_id',
        'fecha_ingreso',
        'fecha_salida',
        'motivo_ingreso',
        'estado'
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'datetime',
            'fecha_salida' => 'datetime',
        ];
    }

    // Vehículo registrado
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    // Mecánico asignado
    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    // Diagnóstico del ingreso
    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class);
    }
}
