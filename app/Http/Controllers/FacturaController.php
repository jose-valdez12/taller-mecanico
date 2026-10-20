<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Reparacion;
use App\Models\DetalleSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FacturaController extends Controller
{
    // Listar facturas
    public function index()
    {
        $facturas = Factura::with([
            'reparacion.diagnostico.ingreso.vehiculo.cliente'
        ])
        ->orderBy('id', 'desc')
        ->get();

        return view('facturas.index', compact('facturas'));
    }

    // Formulario para generar factura
    public function create()
    {
        $reparaciones = Reparacion::with([
            'diagnostico.ingreso.vehiculo.cliente'
        ])
        ->where('estado', 'finalizada')
        ->whereDoesntHave('factura')
        ->orderBy('id', 'desc')
        ->get();

        return view('facturas.create', compact('reparaciones'));
    }

    // Calcular el costo de los repuestos despachados
    private function totalRepuestos(Reparacion $reparacion)
    {
        return DetalleSolicitud::whereHas(
            'solicitud',
            function ($query) use ($reparacion) {
                $query->where('reparacion_id', $reparacion->id)
                      ->where('estado', 'despachada');
            }
        )
        ->selectRaw(
            'COALESCE(SUM(cantidad * precio_unitario), 0) as total'
        )
        ->value('total');
    }

    // Guardar factura
    public function store(Request $request)
    {
        $datos = $request->validate([
            'reparacion_id' => 'required|exists:reparaciones,id'
        ]);

        DB::transaction(function () use ($datos) {

            $reparacion = Reparacion::whereKey(
                $datos['reparacion_id']
            )
            ->lockForUpdate()
            ->firstOrFail();

            // Solo facturar reparaciones finalizadas
            if ($reparacion->estado !== 'finalizada') {
                throw ValidationException::withMessages([
                    'reparacion_id' =>
                        'Solo se pueden facturar reparaciones finalizadas.'
                ]);
            }

            // Evitar facturas duplicadas
            if ($reparacion->factura()->exists()) {
                throw ValidationException::withMessages([
                    'reparacion_id' =>
                        'Esta reparación ya tiene una factura.'
                ]);
            }

            // Verificar que todos los repuestos
            // despachados tengan un precio registrado
            $sinPrecio = DetalleSolicitud::whereHas(
                'solicitud',
                function ($query) use ($reparacion) {
                    $query->where('reparacion_id', $reparacion->id)
                          ->where('estado', 'despachada');
                }
            )
            ->whereNull('precio_unitario')
            ->exists();

            if ($sinPrecio) {
                throw ValidationException::withMessages([
                    'reparacion_id' =>
                        'Existen repuestos despachados sin precio registrado.'
                ]);
            }

            // Calcular costo total de repuestos
            $totalRepuestos = $this->totalRepuestos($reparacion);

            // Sumar mano de obra y repuestos
            $total = round(
                (float) $reparacion->costo_mano_obra +
                (float) $totalRepuestos,
                2
            );

            // Generar factura
            Factura::create([
                'reparacion_id' => $reparacion->id,
                'total' => $total,
                'fecha_emision' => now()->toDateString()
            ]);
        });

        return redirect()->route('facturas.index')
            ->with('success', 'Factura generada correctamente.');
    }

    // Consultar factura
    public function show(Factura $factura)
    {
        $factura->load([
            'reparacion.diagnostico.ingreso.vehiculo.cliente'
        ]);

        // Obtener repuestos con sus precios históricos
        $repuestos = DetalleSolicitud::with('repuesto')
            ->whereHas('solicitud', function ($query) use ($factura) {
                $query->where(
                    'reparacion_id',
                    $factura->reparacion_id
                )
                ->where('estado', 'despachada');
            })
            ->get();

        return view(
            'facturas.show',
            compact('factura', 'repuestos')
        );
    }
}
