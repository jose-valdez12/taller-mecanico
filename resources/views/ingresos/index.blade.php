
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Ingresos | Taller Mecánico</title>

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
        <h3>Ingreso y Salida de Vehículos</h3>

        <a href="{{ route('ingresos.create') }}"
           class="btn btn-primary">
            Registrar Ingreso
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
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
                            <th>Propietario</th>
                            <th>Mecánico</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ingresos as $ingreso)

                        <tr>
                            <td>{{ $ingreso->id }}</td>

                            <td>
                                {{ $ingreso->vehiculo->placa }}
                            </td>

                            <td>
                                {{ $ingreso->vehiculo->marca }}
                                {{ $ingreso->vehiculo->modelo }}
                            </td>

                            <td>
                                {{ $ingreso->vehiculo->cliente->nombre }}
                            </td>

                            <td>
                                {{ $ingreso->mecanico?->name ?? 'Sin asignar' }}
                            </td>

                            <td>
                                {{ $ingreso->fecha_ingreso->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $ingreso->fecha_salida
                                    ? $ingreso->fecha_salida->format('d/m/Y H:i')
                                    : 'Pendiente' }}
                            </td>

                            <td>
                                @if($ingreso->estado === 'entregado')
                                    <span class="badge bg-success">
                                        Entregado
                                    </span>
                                @elseif($ingreso->estado === 'finalizado')
                                    <span class="badge bg-primary">
                                        Finalizado
                                    </span>
                                @elseif($ingreso->estado === 'en_reparacion')
                                    <span class="badge bg-warning text-dark">
                                        En reparación
                                    </span>
                                @elseif($ingreso->estado === 'en_diagnostico')
                                    <span class="badge bg-info text-dark">
                                        En diagnóstico
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Recibido
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if(
                                    $ingreso->estado === 'finalizado' &&
                                    $ingreso->fecha_salida === null
                                )
                                    <form action="{{ route('ingresos.salida', $ingreso) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Confirmar la entrega de este vehículo?')">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-success btn-sm">
                                            Registrar salida
                                        </button>
                                    </form>
                                @elseif($ingreso->estado === 'entregado')
                                    <span class="text-muted">
                                        Entregado
                                    </span>
                                @else
                                    <span class="text-muted">
                                        En proceso
                                    </span>
                                @endif
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No hay ingresos registrados.
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
