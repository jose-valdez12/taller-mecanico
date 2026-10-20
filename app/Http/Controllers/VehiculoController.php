<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    // Listar vehículos
    public function index()
    {
        $vehiculos = Vehiculo::with('cliente')
            ->orderBy('id', 'desc')
            ->get();

        return view('vehiculos.index', compact('vehiculos'));
    }

    // Mostrar formulario de registro
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return view('vehiculos.create', compact('clientes'));
    }

    // Guardar vehículo
    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer|between:1900,2100',
            'color' => 'nullable|string|max:50',
        ]);

        $datos['placa'] = strtoupper(trim($datos['placa']));

        Vehiculo::create($datos);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Vehiculo $vehiculo)
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return view('vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    // Actualizar vehículo
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $datos = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'placa')->ignore($vehiculo->id),
            ],
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer|between:1900,2100',
            'color' => 'nullable|string|max:50',
        ]);

        $datos['placa'] = strtoupper(trim($datos['placa']));

        $vehiculo->update($datos);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }
}
