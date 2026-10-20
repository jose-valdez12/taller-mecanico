
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Nueva Reparación | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:750px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Registrar Reparación</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reparaciones.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Diagnóstico del vehículo
                    </label>

                    <select name="diagnostico_id"
                            class="form-select" required>

                        <option value="">
                            Seleccione un diagnóstico
                        </option>

                        @foreach($diagnosticos as $diagnostico)
                            <option value="{{ $diagnostico->id }}"
                                {{ old('diagnostico_id') == $diagnostico->id ? 'selected' : '' }}>

                                Diagnóstico #{{ $diagnostico->id }} -
                                {{ $diagnostico->ingreso->vehiculo->placa }} -
                                {{ $diagnostico->ingreso->vehiculo->marca }}
                                {{ $diagnostico->ingreso->vehiculo->modelo }}

                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Solo aparecen diagnósticos disponibles para reparación.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Descripción de la reparación
                    </label>

                    <textarea name="descripcion"
                              class="form-control"
                              rows="4"
                              maxlength="5000"
                              required
                              placeholder="Describa los trabajos que se realizarán">{{ old('descripcion') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Costo de mano de obra (Q)
                    </label>

                    <input type="number"
                           name="costo_mano_obra"
                           class="form-control"
                           min="0"
                           max="99999999.99"
                           step="0.01"
                           value="{{ old('costo_mano_obra', 0) }}"
                           required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar Reparación
                </button>

                <a href="{{ route('reparaciones.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>
        </div>
    </div>

</div>

</body>
</html>
