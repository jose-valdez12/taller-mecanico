<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RepuestoController extends Controller
{
    public function index()
    {
        $repuestos = Repuesto::orderBy('nombre')->get();

        return view('repuestos.index', compact('repuestos'));
    }

    public function create()
    {
        return view('repuestos.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:100|unique:repuestos,codigo',
            'stock' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ]);

        $datos['codigo'] = strtoupper(trim($datos['codigo']));

        Repuesto::create($datos);

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto registrado correctamente.');
    }

    public function edit(Repuesto $repuesto)
    {
        return view('repuestos.edit', compact('repuesto'));
    }

    public function update(Request $request, Repuesto $repuesto)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => [
                'required',
                'string',
                'max:100',
                Rule::unique('repuestos', 'codigo')->ignore($repuesto->id)
            ],
            'stock' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ]);

        $datos['codigo'] = strtoupper(trim($datos['codigo']));

        $repuesto->update($datos);

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto actualizado correctamente.');
    }
}
