
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Diagnósticos | Taller Mecánico</title>

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
        <h3>Diagnósticos de Vehículos</h3>

        <a href="{{ route('diagnosticos.create') }}"
           class="btn btn-primary">
            Nuevo Diagnóstico
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
                            <th>Placa</th>
                            <th>Vehículo</th>
                            <th>Cliente</th>
                            <th>Mecánico</th>
                            <th>Fallas Detectadas</th>
                            <th>Reparaciones Necesarias</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($diagnosticos as $diagnostico)

                        <tr>
                            <td>{{ $diagnostico->id }}</td>

                            <td>
                                {{ $diagnostico->ingreso->vehiculo->placa }}
                            </td>

                            <td>
                                {{ $diagnostico->ingreso->vehiculo->marca }}
                                {{ $diagnostico->ingreso->vehiculo->modelo }}
                            </td>

                            <td>
                                {{ $diagnostico->ingreso->vehiculo->cliente->nombre }}
                            </td>

                            <td>
                                {{ $diagnostico->ingreso->mecanico?->name ?? 'Sin asignar' }}
                            </td>

                            <td>
                                {{ $diagnostico->descripcion_fallas }}
                            </td>

                            <td>
                                {{ $diagnostico->reparaciones_necesarias }}
                            </td>

                            <td>
                                {{ $diagnostico->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No hay diagnósticos registrados.
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
