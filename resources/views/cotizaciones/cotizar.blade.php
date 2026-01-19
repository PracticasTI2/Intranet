@extends('layouts/contentNavbarLayout')

@section('title', 'Cotizar Requisición')

@section('content')
    <div class="row">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted">Detalles de la Requisición</span>
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

            <div class="row m-2">
                <form id="formrequisicion" class="mb-3" action="{{ route('cotizaciones-update', $id_requi) }}" method="post"  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                      @foreach ($cotizaciones as $cotizacion)
                            <div class="col-md-12 mb-3">
                                <label for="cotizacion_{{ $cotizacion->id }}" class="form-label">
                                    Archivo de Cotización {{ $loop->iteration }}
                                </label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" value="{{ $cotizacion->archivo }}" disabled>
                                    <a href="{{ route('download-cotizacion', ['file' => $cotizacion->archivo]) }}" class="btn btn-sm btn-primary mx-2">Descargar</a>
                                    <button type="button" class="btn btn-sm btn-danger remove-file" data-id="{{ $cotizacion->id }}">Eliminar</button>
                                </div>
                                <!-- Campo para reemplazar el archivo -->
                                <input type="file" class="form-control mt-2" name="nueva_cotizacion[{{ $cotizacion->id }}]">
                            </div>
                        @endforeach

                        <div class="col-md-12 mb-3">
                            <label for="cotizaciones" class="form-label">Subir nuevas cotizaciones</label>
                            <input type="file" class="form-control" name="nuevas_cotizaciones[]" multiple>
                        </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                        <button class="btn btn-success"> Enviar cotizaciones </button>
                            <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                        </div>
                    </div>

                </form>
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
    $(document).ready(function() {
    // Validar tipo de archivo antes de subir
    $('#cotizaciones').on('change', function() {
        const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'png']; // Extensiones permitidas
        const files = this.files;
        let invalidFile = false;

        for (let i = 0; i < files.length; i++) {
            const fileExtension = files[i].name.split('.').pop().toLowerCase(); // Obtener extensión del archivo
            if (!allowedExtensions.includes(fileExtension)) {
                invalidFile = true;
                break;
            }
        }

        if (invalidFile) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo no compatible',
                text: 'Solo se permiten archivos PDF, DOC, DOCX, JPG o PNG.',
                confirmButtonText: 'OK'
            });
            this.value = ''; // Limpiar el input de archivos
        }
    });

    // Resto del código...
});
</script>

@endsection
