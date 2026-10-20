
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Nueva Solicitud | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:750px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Solicitar Repuesto</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('solicitudes.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Reparación
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
                                {{ $reparacion->diagnostico->ingreso->vehiculo->placa }}

                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Repuesto solicitado
                    </label>

                    <select name="repuesto_id"
                            class="form-select" required>

                        <option value="">
                            Seleccione un repuesto
                        </option>

                        @foreach($repuestos as $repuesto)
                            <option value="{{ $repuesto->id }}"
                                {{ old('repuesto_id') == $repuesto->id ? 'selected' : '' }}>

                                {{ $repuesto->nombre }}
                                ({{ $repuesto->codigo }})

                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Cantidad solicitada
                    </label>

                    <input type="number"
                           name="cantidad"
                           class="form-control"
                           min="1"
                           value="{{ old('cantidad', 1) }}"
                           required>
                </div>

                <div class="alert alert-info">
                    La solicitud será enviada a Bodega.
                    El bodeguero comprobará las existencias
                    antes de autorizar el despacho.
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Enviar Solicitud
                </button>

                <a href="{{ route('solicitudes.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>
        </div>
    </div>
</div>

</body>
</html>
