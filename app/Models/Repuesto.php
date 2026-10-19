<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = 'repuestos';

    protected $fillable = [
        'nombre',
        'codigo',
        'stock',
        'precio'
    ];

    public function detallesSolicitudes()
    {
        return $this->hasMany(
            DetalleSolicitud::class,
            'repuesto_id'
        );
    }
}
