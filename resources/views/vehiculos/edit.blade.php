
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vehículo | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:700px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Editar Vehículo</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('vehiculos.update', $vehiculo) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Cliente propietario</label>

                    <select name="cliente_id" class="form-select" required>
                        <option value="">Seleccione un cliente</option>

                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ old('cliente_id', $vehiculo->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Número de placa</label>
                    <input type="text"
                           name="placa"
                           class="form-control"
                           maxlength="20"
                           value="{{ old('placa', $vehiculo->placa) }}"
                           required>
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Marca</label>
                        <input type="text"
                               name="marca"
                               class="form-control"
                               value="{{ old('marca', $vehiculo->marca) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Modelo</label>
                        <input type="text"
                               name="modelo"
                               class="form-control"
                               value="{{ old('modelo', $vehiculo->modelo) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Año</label>
                        <input type="number"
                               name="anio"
                               class="form-control"
                               min="1900"
                               max="2100"
                               value="{{ old('anio', $vehiculo->anio) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text"
                               name="color"
                               class="form-control"
                               value="{{ old('color', $vehiculo->color) }}">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar Cambios
                </button>

                <a href="{{ route('vehiculos.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>
</div>

</body>
</html>
