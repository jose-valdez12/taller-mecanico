
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Solicitudes de Repuestos | Taller Mecánico</title>

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

    <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
        <h3>Solicitudes de Repuestos</h3>

        <a href="{{ route('solicitudes.create') }}"
           class="btn btn-primary">
            Nueva Solicitud
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Vehículo</th>
                            <th>Mecánico</th>
                            <th>Repuesto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($solicitudes as $solicitud)

                        <tr>
                            <td>{{ $solicitud->id }}</td>

                            <td>
                                {{ $solicitud->reparacion->diagnostico->ingreso->vehiculo->placa }}
                            </td>

                            <td>
                                {{ $solicitud->mecanico->name }}
                            </td>

                            <td>
                                @foreach($solicitud->detalles as $detalle)
                                    {{ $detalle->repuesto->nombre }}
                                @endforeach
                            </td>

                            <td>
                                @foreach($solicitud->detalles as $detalle)
                                    {{ $detalle->cantidad }}
                                @endforeach
                            </td>

                            <td>
                                @if($solicitud->estado === 'pendiente')
                                    <span class="badge bg-warning text-dark">
                                        Pendiente
                                    </span>
                                @elseif($solicitud->estado === 'aprobada')
                                    <span class="badge bg-primary">
                                        Aprobada
                                    </span>
                                @elseif($solicitud->estado === 'despachada')
                                    <span class="badge bg-success">
                                        Despachada
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Rechazada
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $solicitud->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No hay solicitudes registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

</body>
</html>
