<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    protected $table = 'reparaciones';

    protected $fillable = [
        'diagnostico_id',
        'descripcion',
        'costo_mano_obra',
        'estado'
    ];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(
            SolicitudRepuesto::class,
            'reparacion_id'
        );
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
