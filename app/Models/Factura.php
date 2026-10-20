<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'reparacion_id',
        'total',
        'fecha_emision'
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'fecha_emision' => 'date',
        ];
    }

    public function reparacion()
    {
        return $this->belongsTo(
            Reparacion::class,
            'reparacion_id'
        );
    }
}
