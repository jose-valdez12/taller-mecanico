
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Facturas | Taller Mecánico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark" style="background:#172c46">
    <div class="container">
        <span class="navbar-brand">Taller Mecánico</span>
        <a href="{{ route('dashboard') }}"
           class="btn btn-outline-light btn-sm">
            Volver al panel
        </a>
    </div>
</nav>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Facturación</h3>
        <a href="{{ route('facturas.create') }}"
           class="btn btn-primary">
            Generar Factura
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($facturas as $factura)
                        <tr>
                            <td>#{{ $factura->id }}</td>
                            <td>
                                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->cliente->nombre }}
                            </td>
                            <td>
                                {{ $factura->reparacion->diagnostico->ingreso->vehiculo->placa }}
                            </td>
                            <td>
                                {{ $factura->fecha_emision->format('d/m/Y') }}
                            </td>
                            <td>
                                Q {{ number_format($factura->total, 2) }}
                            </td>
                            <td>
                                <a href="{{ route('facturas.show', $factura) }}"
                                   class="btn btn-primary btn-sm">
                                    Ver factura
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay facturas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
