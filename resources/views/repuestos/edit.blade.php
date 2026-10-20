
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Repuesto | Taller Mecánico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:650px">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Editar Repuesto</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('repuestos.update', $repuesto) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input name="codigo" class="form-control"
                           value="{{ old('codigo', $repuesto->codigo) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input name="nombre" class="form-control"
                           value="{{ old('nombre', $repuesto->nombre) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Existencias</label>
                    <input type="number" name="stock" min="0"
                           class="form-control"
                           value="{{ old('stock', $repuesto->stock) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio (Q)</label>
                    <input type="number" name="precio" min="0" step="0.01"
                           class="form-control"
                           value="{{ old('precio', $repuesto->precio) }}" required>
                </div>

                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('repuestos.index') }}"
                   class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
