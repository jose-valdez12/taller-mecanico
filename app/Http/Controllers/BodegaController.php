<?php

namespace App\Http\Controllers;

use App\Models\SolicitudRepuesto;
use App\Models\Repuesto;
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

    // Autorizar o rechazar solicitud
    public function resolver(Request $request, SolicitudRepuesto $solicitud)
    {
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
                $solicitud->update(['estado' => 'rechazada']);
                return;
            }

            // Validación definitiva en el backend
            foreach ($solicitud->detalles as $detalle) {

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

            $solicitud->update(['estado' => 'aprobada']);
        });

        return redirect()->route('bodega.index')
            ->with('success', 'Solicitud procesada correctamente.');
    }

    // Despachar y descontar existencias
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

            // Bloquear y verificar nuevamente el stock
            $detalles = $solicitud->detalles()
                ->orderBy('repuesto_id')
                ->get();

            foreach ($detalles as $detalle) {

                $repuesto = Repuesto::whereKey($detalle->repuesto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($repuesto->stock < $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'stock' =>
                            'Stock insuficiente para ' . $repuesto->nombre
                    ]);
                }

                $repuesto->decrement('stock', $detalle->cantidad);
            }

            $solicitud->update(['estado' => 'despachada']);
        });

        return redirect()->route('bodega.index')
            ->with('success', 'Repuestos despachados correctamente.');
    }
}
