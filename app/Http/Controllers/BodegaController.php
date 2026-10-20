<?php

namespace App\Http\Controllers;

use App\Models\SolicitudRepuesto;
use App\Models\Repuesto;
use App\Services\ExistenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BodegaController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudRepuesto::with([
            'mecanico',
            'reparacion.diagnostico.ingreso.vehiculo',
            'detalles.repuesto'
        ])
        ->orderBy('id', 'desc')
        ->get();

        return view('bodega.index', compact('solicitudes'));
    }

    // Aprobar o rechazar solicitudes
    public function resolver(
        Request $request,
        SolicitudRepuesto $solicitud,
        ExistenciaService $existencias
    ) {
        $datos = $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
        ]);

        DB::transaction(function () use (
            $solicitud, $datos, $existencias
        ) {
            $solicitud = SolicitudRepuesto::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($solicitud->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya fue procesada.'
                ]);
            }

            if ($datos['accion'] === 'rechazar') {
                $solicitud->update([
                    'estado' => 'rechazada'
                ]);
                return;
            }

            if (
                $solicitud->reparacion->estado === 'finalizada' ||
                $solicitud->reparacion->diagnostico->ingreso->fecha_salida !== null
            ) {
                throw ValidationException::withMessages([
                    'solicitud' => 'La reparación ya está finalizada.'
                ]);
            }

            $detalles = $solicitud->detalles()
                ->orderBy('repuesto_id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'solicitud' => 'La solicitud no contiene repuestos.'
                ]);
            }

            foreach ($detalles as $detalle) {
                $repuesto = Repuesto::whereKey($detalle->repuesto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $resultado = $existencias->consultar(
                    $repuesto,
                    (int) $detalle->cantidad
                );

                if (!$resultado['disponible']) {
                    throw ValidationException::withMessages([
                        'stock' => 'Existencias insuficientes de '
                            . $repuesto->nombre
                    ]);
                }
            }

            $solicitud->update([
                'estado' => 'aprobada'
            ]);
        }, 3);

        return redirect()->route('bodega.index')
            ->with('success', 'Solicitud procesada correctamente.');
    }

    // Despachar repuestos
    public function despachar(
        SolicitudRepuesto $solicitud,
        ExistenciaService $existencias
    ) {
        DB::transaction(function () use (
            $solicitud, $existencias
        ) {
            $solicitud = SolicitudRepuesto::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($solicitud->estado !== 'aprobada') {
                throw ValidationException::withMessages([
                    'solicitud' =>
                        'Solo se pueden despachar solicitudes aprobadas.'
                ]);
            }

            if (
                $solicitud->reparacion->estado === 'finalizada' ||
                $solicitud->reparacion->diagnostico->ingreso->fecha_salida !== null
            ) {
                throw ValidationException::withMessages([
                    'solicitud' =>
                        'No se pueden despachar repuestos para un trabajo finalizado.'
                ]);
            }

            $detalles = $solicitud->detalles()
                ->orderBy('repuesto_id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'solicitud' => 'La solicitud no contiene repuestos.'
                ]);
            }

            // Bloquear todos los repuestos y comprobar
            // las existencias antes de descontar.
            $repuestos = [];

            foreach ($detalles as $detalle) {
                $repuesto = Repuesto::whereKey($detalle->repuesto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $resultado = $existencias->consultar(
                    $repuesto,
                    (int) $detalle->cantidad
                );

                if (!$resultado['disponible']) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stock insuficiente para '
                            . $repuesto->nombre
                    ]);
                }

                $repuestos[$detalle->repuesto_id] = $repuesto;
            }

            // Guardar precios y descontar existencias
            foreach ($detalles as $detalle) {
                $repuesto = $repuestos[$detalle->repuesto_id];

                $detalle->update([
                    'precio_unitario' => $repuesto->precio
                ]);

                $repuesto->decrement(
                    'stock',
                    $detalle->cantidad
                );
            }

            $solicitud->update([
                'estado' => 'despachada'
            ]);
        }, 3);

        return redirect()->route('bodega.index')
            ->with('success', 'Repuestos despachados correctamente.');
    }
}
