<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IngresoController extends Controller
{
    // Listar ingresos de vehículos
    public function index()
    {
        $ingresos = Ingreso::with([
            'vehiculo.cliente',
            'mecanico'
        ])
        ->orderBy('id', 'desc')
        ->get();

        return view('ingresos.index', compact('ingresos'));
    }

    // Mostrar formulario de ingreso
    public function create()
    {
        $vehiculos = Vehiculo::with('cliente')
            ->orderBy('placa')
            ->get();

        $mecanicos = User::where('rol', 'mecanico')
            ->orderBy('name')
            ->get();

        return view('ingresos.create', compact(
            'vehiculos',
            'mecanicos'
        ));
    }

    // Registrar ingreso
    public function store(Request $request)
    {
        $datos = $request->validate([
            'vehiculo_id' => [
                'required',
                'exists:vehiculos,id'
            ],
            'mecanico_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('rol', 'mecanico')
            ],
            'motivo_ingreso' => [
                'required',
                'string',
                'max:2000'
            ],
        ]);

        DB::transaction(function () use ($datos) {

            // Evitar dos ingresos abiertos del mismo vehículo
            Vehiculo::whereKey($datos['vehiculo_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $ingresoAbierto = Ingreso::where(
                'vehiculo_id',
                $datos['vehiculo_id']
            )
            ->whereNull('fecha_salida')
            ->exists();

            if ($ingresoAbierto) {
                throw ValidationException::withMessages([
                    'vehiculo_id' =>
                        'Este vehículo ya tiene un ingreso abierto.'
                ]);
            }

            Ingreso::create([
                'vehiculo_id' => $datos['vehiculo_id'],
                'mecanico_id' => $datos['mecanico_id'],
                'motivo_ingreso' => $datos['motivo_ingreso'],
                'fecha_ingreso' => now(),
                'estado' => 'recibido'
            ]);
        });

        return redirect()->route('ingresos.index')
            ->with('success', 'Ingreso registrado correctamente.');
    }

    // Registrar salida del taller
    public function salida(Ingreso $ingreso)
    {
        DB::transaction(function () use ($ingreso) {

            $ingreso = Ingreso::whereKey($ingreso->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($ingreso->fecha_salida !== null) {
                throw ValidationException::withMessages([
                    'salida' => 'Este vehículo ya fue entregado.'
                ]);
            }

            if ($ingreso->estado !== 'finalizado') {
                throw ValidationException::withMessages([
                    'salida' =>
                        'El trabajo debe estar finalizado antes de registrar la salida.'
                ]);
            }

            // Verificar que todas las reparaciones estén finalizadas
            $pendientes = \App\Models\Reparacion::whereHas(
                'diagnostico',
                function ($query) use ($ingreso) {
                    $query->where('ingreso_id', $ingreso->id);
                }
            )
            ->where('estado', '!=', 'finalizada')
            ->exists();

            if ($pendientes) {
                throw ValidationException::withMessages([
                    'salida' =>
                        'Todavía existen reparaciones pendientes.'
                ]);
            }

            $ingreso->update([
                'fecha_salida' => now(),
                'estado' => 'entregado'
            ]);
        });

        return redirect()->route('ingresos.index')
            ->with('success', 'Salida del vehículo registrada correctamente.');
    }
}
