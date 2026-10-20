
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 12px;
        }

        .login-title {
            color: #172c46;
            font-weight: bold;
        }

        .btn-login {
            background-color: #172c46;
            color: white;
        }

        .btn-login:hover {
            background-color: #294b70;
            color: white;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="card login-card shadow mx-auto">
        <div class="card-body p-4">

            <h3 class="login-title text-center">
                Taller Mecánico
            </h3>

            <p class="text-muted text-center mb-4">
                Sistema de Gestión
            </p>

            <h5 class="mb-3">Iniciar sesión</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.procesar') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Correo electrónico
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           placeholder="Ingrese su correo"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Contraseña
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Ingrese su contraseña"
                           required>
                </div>

                <button type="submit"
                        class="btn btn-login w-100">
                    Ingresar
                </button>

            </form>

        </div>
    </div>
</div>

</body>
</html>
