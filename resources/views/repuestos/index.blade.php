
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Taller Mecánico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark" style="background:#172c46">
    <div class="container">
        <span class="navbar-brand">Taller Mecánico</span>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Volver al panel</a>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Inventario de Repuestos</h3>
        <a href="{{ route('repuestos.create') }}" class="btn btn-primary">
            Nuevo Repuesto
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Precio (Q)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repuestos as $repuesto)
                        <tr>
                            <td>{{ $repuesto->id }}</td>
                            <td>{{ $repuesto->codigo }}</td>
                            <td>{{ $repuesto->nombre }}</td>
                            <td>{{ $repuesto->stock }}</td>
                            <td>Q {{ number_format($repuesto->precio, 2) }}</td>
                            <td>
                                <a href="{{ route('repuestos.edit', $repuesto) }}"
                                   class="btn btn-warning btn-sm">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay repuestos registrados.
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
