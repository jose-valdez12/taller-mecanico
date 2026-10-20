
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width:850px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between">
                <h3>Taller Mecánico</h3>
                <h5>Factura #{{ $factura->id }}</h5>
            </div>

            <hr>

            <p>
                <strong>Fecha:</strong>
                {{ $factura->fecha_emision->format('d/m/Y') }}
            </p>

            <p>
                <strong>Cliente:</strong>
                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->cliente->nombre }}
            </p>

            <p>
                <strong>Vehículo:</strong>
                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->marca }}
                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->modelo }}
            </p>

            <p>
                <strong>Placa:</strong>
                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->placa }}
            </p>

            <hr>

            <h5>Detalle de Reparación</h5>

            <p>
                {{ $factura->reparacion->descripcion }}
            </p>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Concepto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mano de obra</td>
                        <td>1</td>
                        <td>
                            Q {{ number_format($factura->reparacion->costo_mano_obra, 2) }}
                        </td>
                        <td>
                            Q {{ number_format($factura->reparacion->costo_mano_obra, 2) }}
                        </td>
                    </tr>

                    @foreach($repuestos as $detalle)
                        <tr>
                            <td>{{ $detalle->repuesto->nombre }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>
                                Q {{ number_format($detalle->repuesto->precio, 2) }}
                            </td>
                            <td>
                                Q {{ number_format($detalle->cantidad * $detalle->repuesto->precio, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="text-end mt-4">
                Total: Q {{ number_format($factura->total, 2) }}
            </h4>

            <div class="mt-4 d-print-none">
                <button onclick="window.print()"
                        class="btn btn-primary">
                    Imprimir
                </button>

                <a href="{{ route('facturas.index') }}"
                   class="btn btn-secondary">
                    Volver
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
