<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DiagnosticoController extends Controller
{
    // Mostrar diagnósticos
    public function index()
    {
        $consulta = Diagnostico::with([
            'ingreso.vehiculo.cliente',
            'ingreso.mecanico'
        ]);

        // El mecánico solo ve sus trabajos
        if (auth()->user()->rol === 'mecanico') {
            $consulta->whereHas('ingreso', function ($query) {
                $query->where('mecanico_id', auth()->id());
            });
        }

        $diagnosticos = $consulta
            ->orderBy('id', 'desc')
            ->get();

        return view('diagnosticos.index', compact('diagnosticos'));
    }

    // Formulario para registrar diagnóstico
    public function create()
    {
        $consulta = Ingreso::with('vehiculo.cliente')
            ->whereNull('fecha_salida')
            ->whereIn('estado', [
                'recibido',
                'en_diagnostico'
            ])
            ->whereDoesntHave('diagnostico');

        if (auth()->user()->rol === 'mecanico') {
            $consulta->where('mecanico_id', auth()->id());
        }

        $ingresos = $consulta
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        return view('diagnosticos.create', compact('ingresos'));
    }

    // Guardar diagnóstico
    public function store(Request $request)
    {
        $datos = $request->validate([
            'ingreso_id' => 'required|exists:ingresos,id',
            'descripcion_fallas' => 'required|string|max:5000',
            'reparaciones_necesarias' => 'required|string|max:5000',
        ]);

        DB::transaction(function () use ($datos) {

            $ingreso = Ingreso::whereKey($datos['ingreso_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Comprobar mecánico responsable
            if (
                auth()->user()->rol === 'mecanico' &&
                (int) $ingreso->mecanico_id !== (int) auth()->id()
            ) {
                abort(403, 'Este vehículo no está asignado a tu usuario.');
            }

            // Comprobar estado del vehículo
            if (
                $ingreso->fecha_salida !== null ||
                !in_array($ingreso->estado, [
                    'recibido',
                    'en_diagnostico'
                ], true)
            ) {
                throw ValidationException::withMessages([
                    'ingreso_id' =>
                        'El vehículo no está disponible para diagnóstico.'
                ]);
            }

            // Evitar duplicar diagnósticos
            if ($ingreso->diagnostico()->exists()) {
                throw ValidationException::withMessages([
                    'ingreso_id' =>
                        'Este ingreso ya tiene un diagnóstico registrado.'
                ]);
            }

            // Registrar diagnóstico
            Diagnostico::create($datos);

            // Actualizar estado del ingreso
            $ingreso->update([
                'estado' => 'en_diagnostico'
            ]);
        });

        return redirect()->route('diagnosticos.index')
            ->with('success', 'Diagnóstico registrado correctamente.');
    }
}
