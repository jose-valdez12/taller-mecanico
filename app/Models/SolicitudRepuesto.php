<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudRepuesto extends Model
{
    protected $table = 'solicitud_repuestos';

    protected $fillable = [
        'reparacion_id',
        'mecanico_id',
        'estado'
    ];

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function detalles()
    {
        return $this->hasMany(
            DetalleSolicitud::class,
            'solicitud_repuesto_id'
        );
    }
}
