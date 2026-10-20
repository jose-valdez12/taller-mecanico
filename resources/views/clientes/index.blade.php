
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Taller Mecánico</title>

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
        <h3>Gestión de Clientes</h3>

        <a href="{{ route('clientes.create') }}"
           class="btn btn-primary">
            Registrar Cliente
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
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>{{ $cliente->correo ?? 'No registrado' }}</td>
                            <td>{{ $cliente->direccion ?? 'No registrada' }}</td>

                            <td>
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="btn btn-warning btn-sm">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay clientes registrados.
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
