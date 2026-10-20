
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Reparaciones | Taller Mecánico</title>

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
        <h3>Gestión de Reparaciones</h3>

        <a href="{{ route('reparaciones.create') }}"
           class="btn btn-primary">
            Nueva Reparación
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
                            <th>Vehículo</th>
                            <th>Mecánico</th>
                            <th>Trabajo</th>
                            <th>Mano de obra</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reparaciones as $reparacion)
                            <tr>
                                <td>{{ $reparacion->id }}</td>

                                <td>
                                    {{ $reparacion->diagnostico->ingreso->vehiculo->placa }}
                                    <br>
                                    {{ $reparacion->diagnostico->ingreso->vehiculo->marca }}
                                    {{ $reparacion->diagnostico->ingreso->vehiculo->modelo }}
                                </td>

                                <td>
                                    {{ $reparacion->diagnostico->ingreso->mecanico?->name ?? 'Sin asignar' }}
                                </td>

                                <td>{{ $reparacion->descripcion }}</td>

                                <td>
                                    Q {{ number_format($reparacion->costo_mano_obra, 2) }}
                                </td>

                                <td>
                                    @if($reparacion->estado === 'pendiente')
                                        <span class="badge bg-secondary">
                                            Pendiente
                                        </span>
                                    @elseif($reparacion->estado === 'en_proceso')
                                        <span class="badge bg-warning text-dark">
                                            En proceso
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            Finalizada
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($reparacion->estado === 'pendiente')
                                        <form method="POST"
                                              action="{{ route('reparaciones.estado', $reparacion) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden"
                                                   name="estado"
                                                   value="en_proceso">

                                            <button class="btn btn-primary btn-sm">
                                                Iniciar reparación
                                            </button>
                                        </form>

                                    @elseif($reparacion->estado === 'en_proceso')
                                        <form method="POST"
                                              action="{{ route('reparaciones.estado', $reparacion) }}"
                                              onsubmit="return confirm('¿Finalizar esta reparación?')">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden"
                                                   name="estado"
                                                   value="finalizada">

                                            <button class="btn btn-success btn-sm">
                                                Finalizar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">
                                            Trabajo terminado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No hay reparaciones registradas.
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
