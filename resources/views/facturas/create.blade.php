
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Generar Factura | Taller Mecánico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:700px">
    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Generar Factura</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('facturas.store') }}"
                  method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Reparación finalizada
                    </label>

                    <select name="reparacion_id"
                            class="form-select" required>
                        <option value="">
                            Seleccione una reparación
                        </option>

                        @foreach($reparaciones as $reparacion)
                            <option value="{{ $reparacion->id }}"
                                {{ old('reparacion_id') == $reparacion->id ? 'selected' : '' }}>
                                Reparación #{{ $reparacion->id }} -
                                {{ $reparacion->diagnostico->ingreso->vehiculo->placa }} -
                                {{ $reparacion->diagnostico->ingreso->vehiculo->cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="alert alert-info">
                    El sistema calculará el total
                    sumando la mano de obra y los repuestos
                    despachados para esta reparación.
                </div>

                <button type="submit" class="btn btn-primary">
                    Generar Factura
                </button>

                <a href="{{ route('facturas.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
