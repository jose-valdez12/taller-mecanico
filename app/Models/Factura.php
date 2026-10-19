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
            'fecha_emision' => 'date',
        ];
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }
}
