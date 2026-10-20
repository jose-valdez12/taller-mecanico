
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bodega | Taller Mecánico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark" style="background:#172c46">
    <div class="container">
        <span class="navbar-brand">Taller Mecánico</span>
        <a href="{{ route('dashboard') }}"
           class="btn btn-outline-light btn-sm">Volver al panel</a>
    </div>
</nav>

<div class="container py-4">
    <h3>Solicitudes de Repuestos - Bodega</h3>
    <p class="text-muted">
        Verifique las existencias antes de aprobar cada solicitud.
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div id="mensaje-api" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Solicitud</th>
                        <th>Mecánico</th>
                        <th>Vehículo</th>
                        <th>Repuesto</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>#{{ $solicitud->id }}</td>
                            <td>{{ $solicitud->mecanico->name }}</td>
                            <td>
                                {{ $solicitud->reparacion->diagnostico->ingreso->vehiculo->placa }}
                            </td>
                            <td>
                                @foreach($solicitud->detalles as $detalle)
                                    <div>{{ $detalle->repuesto->nombre }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($solicitud->detalles as $detalle)
                                    <div>{{ $detalle->cantidad }}</div>
                                @endforeach
                            </td>
                            <td>
                                {{ ucfirst($solicitud->estado) }}
                            </td>
                            <td>
                                @if($solicitud->estado === 'pendiente')
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button"
                                                class="btn btn-primary btn-sm"
                                                onclick="verificarSolicitud(this)">
                                            Consultar API
                                        </button>

                                        <form method="POST"
                                              action="{{ route('bodega.resolver', $solicitud) }}"
                                              class="form-aprobar">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="accion" value="aprobar">

                                            <button type="submit"
                                                    class="btn btn-success btn-sm"
                                                    disabled>
                                                Aprobar
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('bodega.resolver', $solicitud) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="accion" value="rechazar">
                                            <button class="btn btn-danger btn-sm">
                                                Rechazar
                                            </button>
                                        </form>
                                    </div>

                                    <script type="application/json"
                                            class="datos-repuestos">
                                        @json($solicitud->detalles->map(fn($detalle) => [
                                            'url' => route('api.repuestos.existencias', $detalle->repuesto_id),
                                            'cantidad' => $detalle->cantidad
                                        ])->values())
                                    </script>

                                @elseif($solicitud->estado === 'aprobada')
                                    <form method="POST"
                                          action="{{ route('bodega.despachar', $solicitud) }}"
                                          onsubmit="return confirm('¿Confirmar despacho de repuestos?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success btn-sm">
                                            Despachar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">Procesada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No hay solicitudes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function verificarSolicitud(boton) {
    const celda = boton.closest('td');
    const datos = celda.querySelector('.datos-repuestos');
    const formulario = celda.querySelector('.form-aprobar');
    const btnAprobar = formulario.querySelector('button');

    const mensaje = document.getElementById('mensaje-api');
    const detalles = JSON.parse(datos.textContent);

    btnAprobar.disabled = true;
    boton.disabled = true;

    try {
        for (const detalle of detalles) {
            const url = new URL(detalle.url, window.location.origin);
            url.searchParams.set('cantidad', detalle.cantidad);

            const respuesta = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo consultar la API.');
            }

            const resultado = await respuesta.json();

            if (!resultado.disponible) {
                throw new Error(
                    'Stock insuficiente para ' + resultado.nombre +
                    '. Disponible: ' + resultado.stock
                );
            }
        }

        btnAprobar.disabled = false;
        mensaje.className = 'alert alert-success';
        mensaje.textContent =
            'API consultada: hay existencias suficientes. Puede aprobar la solicitud.';

    } catch (error) {
        mensaje.className = 'alert alert-danger';
        mensaje.textContent = error.message;
    } finally {
        boton.disabled = false;
    }
}
</script>

</body>
</html>
