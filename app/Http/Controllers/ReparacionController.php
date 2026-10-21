<?php

namespace App\Http\Controllers;

use App\Models\Reparacion;
use App\Models\Diagnostico;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReparacionController extends Controller
{
    // Listar reparaciones
    public function index()
    {
        $consulta = Reparacion::with([
            'diagnostico.ingreso.vehiculo.cliente',
            'diagnostico.ingreso.mecanico'
        ]);

        if (auth()->user()->rol === 'mecanico') {
            $consulta->whereHas('diagnostico.ingreso', function ($query) {
                $query->where('mecanico_id', auth()->id());
            });
        }

        $reparaciones = $consulta
            ->orderBy('id', 'desc')
            ->get();

        return view('reparaciones.index', compact('reparaciones'));
    }

    // Formulario de nueva reparación
    public function create()
    {
        $consulta = Diagnostico::with('ingreso.vehiculo')
            ->whereHas('ingreso', function ($query) {

                $query->whereNull('fecha_salida')
                    ->where('estado', 'en_diagnostico');

                if (auth()->user()->rol === 'mecanico') {
                    $query->where('mecanico_id', auth()->id());
                }
            })
            ->whereDoesntHave('reparacion');

        $diagnosticos = $consulta
            ->orderBy('id', 'desc')
            ->get();

        return view('reparaciones.create', compact('diagnosticos'));
    }

    // Registrar reparación
    public function store(Request $request)
    {
        $datos = $request->validate([
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'descripcion' => 'required|string|max:5000',
            'costo_mano_obra' => 'required|numeric|min:0|max:99999999.99',
        ]);

        DB::transaction(function () use ($datos) {

            $diagnostico = Diagnostico::findOrFail(
                $datos['diagnostico_id']
            );

            $ingreso = Ingreso::whereKey($diagnostico->ingreso_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Comprobar mecánico responsable
            if (
                auth()->user()->rol === 'mecanico' &&
                (int) $ingreso->mecanico_id !== (int) auth()->id()
            ) {
                abort(403, 'Este trabajo no está asignado a tu usuario.');
            }

            // Comprobar disponibilidad del ingreso
            if (
                $ingreso->fecha_salida !== null ||
                $ingreso->estado !== 'en_diagnostico'
            ) {
                throw ValidationException::withMessages([
                    'diagnostico_id' =>
                        'El ingreso no está disponible para crear una reparación.'
                ]);
            }

            // Evitar dos reparaciones para un diagnóstico
            if ($diagnostico->reparacion()->exists()) {
                throw ValidationException::withMessages([
                    'diagnostico_id' =>
                        'Este diagnóstico ya tiene una reparación registrada.'
                ]);
            }

            // Registrar reparación
            Reparacion::create([
                'diagnostico_id' => $diagnostico->id,
                'descripcion' => $datos['descripcion'],
                'costo_mano_obra' => $datos['costo_mano_obra'],
                'estado' => 'pendiente'
            ]);

            // Actualizar estado del ingreso
            $ingreso->update([
                'estado' => 'en_reparacion'
            ]);
        });

        return redirect()->route('reparaciones.index')
            ->with('success', 'Reparación registrada correctamente.');
    }

    // Actualizar estado de la reparación
    public function actualizarEstado(
        Request $request,
        Reparacion $reparacion
    ) {
        $datos = $request->validate([
            'estado' => [
                'required',
                Rule::in(['en_proceso', 'finalizada'])
            ],
        ]);

        DB::transaction(function () use ($reparacion, $datos) {

            $ingresoId = $reparacion->diagnostico->ingreso_id;

            $ingreso = Ingreso::whereKey($ingresoId)
                ->lockForUpdate()
                ->firstOrFail();

            $reparacion = Reparacion::whereKey($reparacion->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Comprobar permisos del mecánico
            if (
                auth()->user()->rol === 'mecanico' &&
                (int) $ingreso->mecanico_id !== (int) auth()->id()
            ) {
                abort(403, 'No tienes permiso para modificar este trabajo.');
            }

            // Comprobar estado actual
            if (
                $ingreso->fecha_salida !== null ||
                $ingreso->estado !== 'en_reparacion' ||
                $reparacion->estado === 'finalizada'
            ) {
                throw ValidationException::withMessages([
                    'estado' => 'Esta reparación no se puede modificar.'
                ]);
            }

            // Iniciar reparación
            if (
                $reparacion->estado === 'pendiente' &&
                $datos['estado'] !== 'en_proceso'
            ) {
                throw ValidationException::withMessages([
                    'estado' =>
                        'Primero debes iniciar la reparación.'
                ]);
            }

            // Finalizar reparación
            if (
                $reparacion->estado === 'en_proceso' &&
                $datos['estado'] !== 'finalizada'
            ) {
                throw ValidationException::withMessages([
                    'estado' =>
                        'La transición de estado no es válida.'
                ]);
            }

            if ($datos['estado'] === 'finalizada') {

                // Verificar solicitudes pendientes
                if (
                    $reparacion->solicitudes()
                        ->whereIn('estado', [
                            'pendiente',
                            'aprobada'
                        ])
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'estado' =>
                            'Existen solicitudes de repuestos pendientes de despacho.'
                    ]);
                }

                // Comprobar otras reparaciones pendientes
                $otrasPendientes = Reparacion::whereHas(
                    'diagnostico',
                    function ($query) use ($ingreso) {
                        $query->where(
                            'ingreso_id',
                            $ingreso->id
                        );
                    }
                )
                ->where('id', '!=', $reparacion->id)
                ->where('estado', '!=', 'finalizada')
                ->exists();

                $reparacion->update([
                    'estado' => 'finalizada'
                ]);

                if (!$otrasPendientes) {
                    $ingreso->update([
                        'estado' => 'finalizado'
                    ]);
                }

            } else {

                $reparacion->update([
                    'estado' => 'en_proceso'
                ]);
            }
        });

        return redirect()->route('reparaciones.index')
            ->with('success', 'Estado actualizado correctamente.');
    }
}
