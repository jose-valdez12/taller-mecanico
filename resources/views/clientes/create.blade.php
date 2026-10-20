
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width:650px">

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h3 class="mb-4">Registrar Cliente</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('clientes.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text"
                           name="telefono"
                           class="form-control"
                           maxlength="20"
                           value="{{ old('telefono') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email"
                           name="correo"
                           class="form-control"
                           value="{{ old('correo') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text"
                           name="direccion"
                           class="form-control"
                           value="{{ old('direccion') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar Cliente
                </button>

                <a href="{{ route('clientes.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>
</div>

</body>
</html>
