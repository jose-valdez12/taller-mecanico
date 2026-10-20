<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repuesto;
use Illuminate\Http\JsonResponse;

class ExistenciaController extends Controller
{
    public function consultar(Repuesto $repuesto): JsonResponse
    {
        $cantidad = filter_var(
            request()->query('cantidad', 1),
            FILTER_VALIDATE_INT
        );

        if ($cantidad === false || $cantidad < 1) {
            return response()->json([
                'mensaje' => 'La cantidad debe ser un entero positivo.'
            ], 422);
        }

        return response()->json([
            'repuesto_id' => $repuesto->id,
            'codigo' => $repuesto->codigo,
            'nombre' => $repuesto->nombre,
            'stock' => $repuesto->stock,
            'cantidad_solicitada' => $cantidad,
            'disponible' => $repuesto->stock >= $cantidad,
        ]);
    }
}
