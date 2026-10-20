
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registrar Diagnóstico | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:750px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Registrar Diagnóstico</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('diagnosticos.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Vehículo Asignado
                    </label>

                    <select name="ingreso_id"
                            class="form-select" required>

                        <option value="">
                            Seleccione un vehículo
                        </option>

                        @foreach($ingresos as $ingreso)
                            <option value="{{ $ingreso->id }}"
                                {{ old('ingreso_id') == $ingreso->id ? 'selected' : '' }}>

                                {{ $ingreso->vehiculo->placa }} -
                                {{ $ingreso->vehiculo->marca }}
                                {{ $ingreso->vehiculo->modelo }} -
                                {{ $ingreso->vehiculo->cliente->nombre }}

                            </option>
                        @endforeach

                    </select>

                    <small class="text-muted">
                        Solo aparecen vehículos pendientes de diagnóstico.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Descripción de Fallas
                    </label>

                    <textarea name="descripcion_fallas"
                              class="form-control"
                              rows="4"
                              maxlength="5000"
                              placeholder="Describa las fallas detectadas"
                              required>{{ old('descripcion_fallas') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Reparaciones Necesarias
                    </label>

                    <textarea name="reparaciones_necesarias"
                              class="form-control"
                              rows="4"
                              maxlength="5000"
                              placeholder="Indique los trabajos necesarios para reparar el vehículo"
                              required>{{ old('reparaciones_necesarias') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Guardar Diagnóstico
                </button>

                <a href="{{ route('diagnosticos.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
