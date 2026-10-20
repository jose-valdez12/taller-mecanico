<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repuesto;
use App\Services\ExistenciaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExistenciaController extends Controller
{
    public function consultar(
        Request $request,
        Repuesto $repuesto,
        ExistenciaService $existencias
    ): JsonResponse {
        $datos = $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        return response()->json(
            $existencias->consultar(
                $repuesto,
                (int) $datos['cantidad']
            )
        );
    }
}
