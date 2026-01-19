@extends('layouts/contentNavbarLayout')

@section('title', ' Requisición Completada')

@section('content')
    <div class="row">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted">Requisicion Completa</span>
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

                                <th>Requisicion</th>
                                <th>Cotizacion</th>
                                <th>Factura</th>

                            </tr>
                        </thead>
                        <tbody id="cotizaciones_body">

                                <tr>
                                    <td><a class="btn btn-dark p-1 me-4" href="{{ route('requisicion.pdf', $id_requi) }}">

                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                                                        viewBox="0 0 512 512">
                                                        <path fill="#f3f4f7"
                                                            d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                                                    </svg>
                                        </a></td>

                                    <td>
                                        <!-- Enlace para visualizar el archivo -->
                                        <a href="{{ route('view-cotizacion', ['file' => $requisiciones->archivo]) }}"
                                            class="btn btn-sm btn-dark" target="_blank"> <i class='bx bx-download'></i> </a>

                                    </td>

                                   <td>
                                        @if(empty($requisiciones->factura) || is_null($requisiciones->factura) || empty($requisiciones->xml_factura) || is_null($requisiciones->xml_factura))
                                        <!-- Si NO hay factura o XML, muestra la opción para subir ambos -->
                                        <form id="formFactura" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="factura" class="form-label">Subir factura (PDF, JPG, PNG)</label>
                                                <input class="form-control" type="file" name="factura" id="factura" accept=".pdf,.jpg,.jpeg,.png" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="xml_factura" class="form-label">Subir XML de la factura</label>
                                                <input class="form-control" type="file" name="xml_factura" id="xml_factura" accept=".xml" required>
                                            </div>
                                            <div class="mt-4 col-md-5">
                                                <button type="button" id="btnSubirFactura" class="btn btn-success mt-2">Guardar Factura y XML</button>
                                            </div>
                                        </div>
                                    </form>
                                        @elseif($requisiciones->factura && $requisiciones->xml_factura)
                                            <!-- Si ya existen ambos archivos, muestra los botones para verlos -->
                                            <a href="{{ route('view-cotizacion', ['file' => $requisiciones->factura]) }}" class="btn btn-dark" target="_blank">
                                                <i class='bx bx-credit-card-front'></i> Ver Factura
                                            </a>
                                            <a href="{{ route('view-cotizacion', ['file' => $requisiciones->xml_factura]) }}" class="btn btn-dark" target="_blank">
                                                <i class='bx bx-file'></i> Ver XML de la Factura
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                        </tbody>
                    </table>
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

    document.getElementById("btnSubirFactura").addEventListener("click", function() {
    let facturaFile = document.getElementById("factura").files[0];
    let xmlFile = document.getElementById("xml_factura").files[0];

    if (!facturaFile || !xmlFile) {
        Swal.fire({
            icon: "warning",
            title: "¡Atención!",
            text: "Por favor, selecciona tanto la factura como el XML antes de subir.",
        });
        return;
    }

    let formData = new FormData();
    formData.append("factura", facturaFile);
    formData.append("xml_factura", xmlFile);
    formData.append("_token", "{{ csrf_token() }}");

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Se subirá la factura y el XML seleccionados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, subir'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('guardar.factura', ['requisicion_id' => $id_requi]) }}", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                    }).then(() => {
                        location.reload(); // Recarga la página para mostrar el archivo subido
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Hubo un problema al subir la factura.',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo completar la solicitud.',
                });
            });
        }
    });
});
</script>
@endsection
