@extends('layouts/contentNavbarLayout')

@section('title', 'Cotizar Requisición')

@section('content')

    <div class="row">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted">Autorizar cotizacion</span>
        </h4>

        <div class="col-md-12">
            <div class="card">
                <div class="m-2">

                    <h5 class="text-center text-uppercase"> EMPRESA: {{ $requisiciones->rfc }} <strong> FOLIO :
                            {{ $requisiciones->folio }}</strong> </h5>

                    <h5 class="text-center text-uppercase"> Solicita : {{ $requisiciones->solicitante }} AREA :
                        {{ $requisiciones->area }}</h5>

                    <h5 class="text-center text-uppercase"> Fecha Solicitud :
                        {{ \Carbon\Carbon::parse($requisiciones->fechasol)->format('d/m/Y') }}
                        Estado : @if ($requisiciones->estado == 1)
                            <strong> Pendiente </strong>
                        @elseif($requisiciones->estado == 2)
                            <strong> Autorizado </strong>
                        @elseif($requisiciones->estado == 3)
                            <strong> Rechazado </strong>
                        @elseif($requisiciones->estado == 4)
                            <strong> Preautorizado </strong>
                        @elseif($requisiciones->estado == 5)
                            <strong> Entregado </strong>
                        @elseif($requisiciones->estado == 6)
                            <strong> Capturado </strong>
                        @elseif($requisiciones->estado == 7)
                            <strong> Cotizado </strong>
                        @elseif($requisiciones->estado == 8)
                            <strong> Autorizado No Cotizar </strong>
                        @elseif($requisiciones->estado == 9)
                            <strong> Autorizado Necesita Cotización </strong>
                        @elseif($requisiciones->estado == 10)
                            <strong> Liberada </strong>
                        @endif
                    </h5>

                    @if ($requisiciones->idtipo_insumo == 1)
                        <h5 class="text-center text-uppercase">Tipo : <strong> Consumibles </strong></h5>
                    @else
                        <h5 class="text-center text-uppercase">Tipo : <strong> Especiales </strong></h5>
                    @endif
                </div>

              <div class="table-responsive mx-2 my-4">
                    <h6 class="text-center text-uppercase">Cotizaciones Guardadas</h6>
                    <table class="table table-striped table-borderless border-bottom p-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Archivo</th>
                                <th>Ver/Descargar</th>
                                <th>Selecciona</th>

                            </tr>
                        </thead>
                        <tbody id="cotizaciones_body">
                            @foreach ($cotizaciones as $cotizacion)
                                <tr>
                                    <td>{{ $cotizacion->id }}</td>
                                    <td>{{ $cotizacion->archivo }}</td>
                                    <td>
                                        <!-- Enlace para visualizar el archivo -->
                                        <a href="{{ route('view-cotizacion', ['file' => $cotizacion->archivo]) }}"
                                            class="btn btn-sm btn-info" target="_blank">Ver</a>
                                        <!-- Enlace para descargar el archivo -->
                                        <a href="{{ route('download-cotizacion', ['file' => $cotizacion->archivo]) }}"
                                            class="btn btn-sm btn-primary">Descargar</a>
                                    </td>
                                    <td>
                                                <!-- Radio button para seleccionar solo una cotización -->
                                        <input type="radio" id="cotizacion_elegir" name="cotizacion_elegir" value="{{ $cotizacion->id }}">
                                    </td>

                                </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="text-center my-4">
                        <button id="autorizarCotizacion" class="btn btn-success">Autorizar Cotización</button>

                        <button id="btnRechazar" class="btn btn-danger mx-4" data-requisicion-id="{{ $id_requi }}">
                            Rechazar Cotizaciones
                        </button>

                    </div>
                </div>

            <div class="row m-2">
                <div class="table-responsive pt-4 m-2">
                    <table class="table table-striped table-borderless border-bottom p-2 ">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Descripción</th>
                                <th>Justificación</th>
                                <!-- <th>Costo</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $index => $req)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $req->cantidad }}</td>
                                    <td>{{ $req->unidad }}</td>
                                    <td>{{ $req->descripcion }}</td>
                                    <td>{{ $req->justificacion }}</td>
                                    <!-- <td><input type="text" id="precio" name="precio"  disabled> </td> -->

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>

                <div class="row m-2">
                    <div class="col-md-12">
                        @if(!empty($requisiciones->respuestapreautoriza))
                            <p>Respuesta Preautorizada : {{ $requisiciones->respuestapreautoriza }}</p>
                        @endif

                        @if(!empty($requisiciones->respuesta))
                            <p>Respuesta Autorizada/Rechazada : {{ $requisiciones->respuesta }}</p>
                        @endif
                    </div>
                </div>



            </div>



        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    $(document).ready(function () {
    $('#autorizarCotizacion').click(function () {
        let cotizacionSeleccionada = $('input[name="cotizacion_elegir"]:checked').val();

        if (!cotizacionSeleccionada) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, selecciona una cotización antes de autorizar.',
            });
            return;
        }

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción autorizará la cotización seleccionada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, autorizar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('autorizar-cotizacion') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cotizacion_id: cotizacionSeleccionada
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Autorizado',
                            text: response.message,
                        }).then(() => {
                            window.location.href = response.redirect_url; // Redirección aquí
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al autorizar la cotización.',
                        });
                    }
                });
            }
        });
    });
});
</script>
<script>
    $(document).ready(function () {
        $('#btnRechazar').on('click', function () {
            let requisicionId = $(this).data('requisicion-id'); // Obtener el ID de la requisición

            var APP_URL = {!! json_encode(url('/')) !!};

            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se eliminarán todas las cotizaciones de esta requisición.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: APP_URL + '/rechazar-cotizaciones',
                        type: 'POST',
                        data: {
                            requisicion_id: requisicionId,
                            _token: $('meta[name="csrf-token"]').attr('content') // Obtener token CSRF
                        },
                        success: function (data) {
                            if (data.success) {
                                Swal.fire("¡Eliminadas!", data.message, "success").then(() => {
                                    // Redirigir a la nueva ruta
                                    window.location.href = data.redirect_url;
                                });
                            } else {
                                Swal.fire("Error", "No se pudieron eliminar las cotizaciones.", "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error", "Ocurrió un problema en el servidor.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

@endsection
