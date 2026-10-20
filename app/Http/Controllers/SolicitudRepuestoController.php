<?php

namespace App\Http\Controllers;

use App\Models\SolicitudRepuesto;
use App\Models\DetalleSolicitud;
use App\Models\Reparacion;
use App\Models\Repuesto;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SolicitudRepuestoController extends Controller
{
    // Listar solicitudes
    public function index()
    {
        $consulta = SolicitudRepuesto::with([
            'reparacion.diagnostico.ingreso.vehiculo',
            'mecanico',
            'detalles.repuesto'
        ]);

        if (auth()->user()->rol === 'mecanico') {
            $consulta->where('mecanico_id', auth()->id());
        }

        $solicitudes = $consulta
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'solicitudes.index',
            compact('solicitudes')
        );
    }

    // Formulario de solicitud
    public function create()
    {
        $consulta = Reparacion::with(
            'diagnostico.ingreso.vehiculo'
        )->whereIn('estado', [
            'pendiente',
            'en_proceso'
        ])->whereHas('diagnostico.ingreso', function ($query) {
            $query->whereNull('fecha_salida')
                  ->where('estado', 'en_reparacion');

            if (auth()->user()->rol === 'mecanico') {
                $query->where('mecanico_id', auth()->id());
            }
        });

        $reparaciones = $consulta->orderBy('id', 'desc')->get();

        $repuestos = Repuesto::orderBy('nombre')->get();

        return view('solicitudes.create', compact(
            'reparaciones',
            'repuestos'
        ));
    }

    // Guardar solicitud
    public function store(Request $request)
    {
        $datos = $request->validate([
            'reparacion_id' => 'required|exists:reparaciones,id',
            'repuesto_id' => 'required|exists:repuestos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($datos) {

            $reparacion = Reparacion::findOrFail(
                $datos['reparacion_id']
            );

            $ingreso = Ingreso::whereKey(
                $reparacion->diagnostico->ingreso_id
            )->lockForUpdate()->firstOrFail();

            if (
                auth()->user()->rol === 'mecanico' &&
                $ingreso->mecanico_id !== auth()->id()
            ) {
                abort(403, 'Esta reparación no está asignada a tu usuario.');
            }

            if (
                $ingreso->fecha_salida !== null ||
                $ingreso->estado !== 'en_reparacion' ||
                !in_array($reparacion->estado, [
                    'pendiente',
                    'en_proceso'
                ])
            ) {
                throw ValidationException::withMessages([
                    'reparacion_id' =>
                        'Esta reparación no permite solicitar repuestos.'
                ]);
            }

            $solicitud = SolicitudRepuesto::create([
                'reparacion_id' => $reparacion->id,
                'mecanico_id' => $ingreso->mecanico_id,
                'estado' => 'pendiente'
            ]);

            DetalleSolicitud::create([
                'solicitud_repuesto_id' => $solicitud->id,
                'repuesto_id' => $datos['repuesto_id'],
                'cantidad' => $datos['cantidad']
            ]);
        });

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud enviada a bodega correctamente.');
    }
}
