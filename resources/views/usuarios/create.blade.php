
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Usuario - Taller Mecánico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:650px">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Registrar usuario</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name"
                           class="form-control"
                           value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email"
                           class="form-control"
                           value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password"
                           class="form-control" minlength="8" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" minlength="8" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="">Seleccione un rol</option>
                        @foreach(['administrador', 'recepcion', 'mecanico', 'bodeguero'] as $rol)
                            <option value="{{ $rol }}" {{ old('rol') === $rol ? 'selected' : '' }}>
                                {{ ucfirst($rol) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary">Guardar usuario</button>
                <a href="{{ route('usuarios.index') }}"
                   class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
