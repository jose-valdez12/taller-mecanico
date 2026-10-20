
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #172c46;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: 0.2s;
        }

        .card h5 {
            color: #172c46;
            font-weight: bold;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .rol {
            background-color: #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand">
            Taller Mecánico
        </span>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="btn btn-outline-light btn-sm">
                Cerrar sesión
            </button>
        </form>
    </div>
</nav>

<div class="container py-4">

    <h3>Panel Principal</h3>

    <p class="text-muted">
        Bienvenido, {{ auth()->user()->name }}
    </p>

    <div class="rol mb-4">
        <strong>Rol:</strong>
        {{ ucfirst(auth()->user()->rol) }}
    </div>

    <div class="row g-3">

        <!-- ADMINISTRADOR -->
        @if(auth()->user()->rol === 'administrador')

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Administración</h5>
                    <p class="text-muted">
                        Gestión de usuarios, roles
                        y permisos del sistema.
                    </p>

                    <a href="{{ route('usuarios.index') }}"
                       class="btn btn-primary">
                        Gestionar usuarios
                    </a>

                        <a href="{{ route('clientes.index') }}"
                         class="btn btn-primary">
                         Gestionar clientes
                        </a>

                        <a href="{{ route('vehiculos.index') }}"
                         class="btn btn-primary">
                         Gestionar vehículos
                        </a>

                        <a href="{{ route('ingresos.index') }}"
                           class="btn btn-primary">
                           Ingresos y Salidas
                        </a>
                </div>
            </div>
        </div>

        @endif

        <!-- RECEPCIÓN -->
        @if(in_array(auth()->user()->rol, ['administrador', 'recepcion']))

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Recepción</h5>
                    <p class="text-muted">
                        Registro de clientes, vehículos,
                        ingresos y salidas.
                    </p>
                </div>
            </div>
        </div>

        @endif

        <!-- MECÁNICO -->
        @if(in_array(auth()->user()->rol, ['administrador', 'mecanico']))

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Mecánica</h5>
                    <p class="text-muted">
                        Diagnósticos, reparaciones
                        y solicitudes de repuestos.
                    </p>
                    <a href="{{ route('diagnosticos.index') }}"
                         class="btn btn-primary">
                        Gestionar Diagnósticos
                    </a>

                     <a href="{{ route('reparaciones.index') }}"
                         class="btn btn-primary">
                         Gestionar Reparaciones
                     </a>
                </div>
            </div>
        </div>

        @endif

        <!-- BODEGUERO -->
        @if(in_array(auth()->user()->rol, ['administrador', 'bodeguero']))

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Bodega</h5>
                    <p class="text-muted">
                        Inventario, existencias
                        y despacho de repuestos.
                    </p>
                </div>
            </div>
        </div>

        @endif

    </div>

</div>

</body>
</html>
