<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSolicitud extends Model
{
    protected $table = 'detalle_solicitudes';

    protected $fillable = [
        'solicitud_repuesto_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario'
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_unitario' => 'decimal:2',
        ];
    }

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
