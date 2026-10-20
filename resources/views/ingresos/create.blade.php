
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Registrar Ingreso | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:700px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Registrar Ingreso de Vehículo</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ingresos.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Vehículo
                    </label>

                    <select name="vehiculo_id"
                            class="form-select" required>

                        <option value="">
                            Seleccione un vehículo
                        </option>

                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}"
                                {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>

                                {{ $vehiculo->placa }} -
                                {{ $vehiculo->marca }}
                                {{ $vehiculo->modelo }} -
                                {{ $vehiculo->cliente->nombre }}

                            </option>
                        @endforeach

                    </select>

                    <small class="text-muted">
                        El vehículo debe estar registrado previamente.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Mecánico responsable
                    </label>

                    <select name="mecanico_id"
                            class="form-select" required>

                        <option value="">
                            Seleccione un mecánico
                        </option>

                        @foreach($mecanicos as $mecanico)
                            <option value="{{ $mecanico->id }}"
                                {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                                {{ $mecanico->name }}
                            </option>
                        @endforeach

                    </select>

                    <small class="text-muted">
                        Solo aparecen usuarios con rol de mecánico.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Motivo de ingreso
                    </label>

                    <textarea name="motivo_ingreso"
                              class="form-control"
                              rows="4"
                              maxlength="2000"
                              placeholder="Describa el problema reportado por el cliente"
                              required>{{ old('motivo_ingreso') }}</textarea>
                </div>

                <div class="alert alert-light border">
                    La fecha y hora de ingreso se registrarán
                    automáticamente.
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Guardar Ingreso
                </button>

                <a href="{{ route('ingresos.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>
        </div>
    </div>

</div>

</body>
</html>
