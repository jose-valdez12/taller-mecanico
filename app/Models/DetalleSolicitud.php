<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSolicitud extends Model
{
    protected $table = 'detalle_solicitudes';

    protected $fillable = [
        'solicitud_repuesto_id',
        'repuesto_id',
        'cantidad'
    ];

    public function solicitud()
    {
        return $this->belongsTo(
            SolicitudRepuesto::class,
            'solicitud_repuesto_id'
        );
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
}
