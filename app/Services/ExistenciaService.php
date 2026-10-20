<?php

namespace App\Services;

use App\Models\Repuesto;

class ExistenciaService
{
    public function consultar(Repuesto $repuesto, int $cantidad): array
    {
        return [
            'repuesto_id' => $repuesto->id,
            'codigo' => $repuesto->codigo,
            'nombre' => $repuesto->nombre,
            'stock' => (int) $repuesto->stock,
            'cantidad_solicitada' => $cantidad,
            'disponible' => $cantidad > 0 &&
                $repuesto->stock >= $cantidad,
        ];
    }
}
