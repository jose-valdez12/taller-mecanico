<?php

namespace App\Http\Controllers;

use App\Models\SolicitudRepuesto;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BodegaController extends Controller
{
    // Listar solicitudes de repuestos
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

    // Aprobar o rechazar una solicitud
    public function resolver(
        Request $request,
        SolicitudRepuesto $solicitud
    ) {
        $datos = $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
        ]);

        DB::transaction(function () use ($solicitud, $datos) {

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

            // Verificar las cantidades solicitadas
            $detalles = $solicitud->detalles()
                ->orderBy('repuesto_id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'solicitud' => 'La solicitud no contiene repuestos.'
                ]);
            }

            foreach ($detalles as $detalle) {

                if ($detalle->cantidad < 1) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'La cantidad solicitada no es válida.'
                    ]);
                }

                $repuesto = Repuesto::whereKey($detalle->repuesto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($repuesto->stock < $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'stock' => 'No hay existencias suficientes de '
                            . $repuesto->nombre
                    ]);
                }
            }

            // Aprobar solicitud
            $solicitud->update([
                'estado' => 'aprobada'
            ]);
        });

        return redirect()->route('bodega.index')
            ->with('success', 'Solicitud procesada correctamente.');
    }

    // Despachar repuestos y descontar existencias
    public function despachar(SolicitudRepuesto $solicitud)
    {
        DB::transaction(function () use ($solicitud) {

            $solicitud = SolicitudRepuesto::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($solicitud->estado !== 'aprobada') {
                throw ValidationException::withMessages([
                    'solicitud' =>
                        'Solo se pueden despachar solicitudes aprobadas.'
                ]);
            }

            // Obtener detalles en orden
            $detalles = $solicitud->detalles()
                ->orderBy('repuesto_id')
                ->get();

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'solicitud' => 'La solicitud no contiene repuestos.'
                ]);
            }

            foreach ($detalles as $detalle) {

                // Bloquear el repuesto durante la operación
                $repuesto = Repuesto::whereKey($detalle->repuesto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Verificar nuevamente las existencias
                if (
                    $detalle->cantidad < 1 ||
                    $repuesto->stock < $detalle->cantidad
                ) {
                    throw ValidationException::withMessages([
                        'stock' =>
                            'Stock insuficiente para ' . $repuesto->nombre
                    ]);
                }

                // Guardar precio histórico del repuesto
                $detalle->update([
                    'precio_unitario' => $repuesto->precio
                ]);

                // Descontar las unidades del inventario
                $repuesto->decrement(
                    'stock',
                    $detalle->cantidad
                );
            }

            // Marcar la solicitud como despachada
            $solicitud->update([
                'estado' => 'despachada'
            ]);
        });

        return redirect()->route('bodega.index')
            ->with(
                'success',
                'Repuestos despachados correctamente.'
            );
    }
}
