@extends('layouts/contentNavbarLayout')

@section('title', 'Editar- Requisicion')

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
       document.addEventListener('DOMContentLoaded', function () {
    const APP_URL = {!! json_encode(url('/')) !!};

    document.getElementById('addRowBtn').addEventListener('click', function () {
        const template = document.getElementById('rowTemplate').content.cloneNode(true);
        document.getElementById('rowsContainer').appendChild(template);
    });

    document.getElementById('rowsContainer').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.row').remove();
        }
    });

    document.getElementById('formrequisicion').addEventListener('submit', function (event) {
        const rows = document.getElementsByClassName('row');
        if (rows.length === 0) {
            event.preventDefault();
            Swal.fire('Error', 'Debe agregar al menos un artículo.', 'error');
        }
    });

    // // Validar tipo de archivo para cotización individual
    // document.querySelectorAll('input[name^="nueva_cotizacion["]').forEach(input => {
    //     input.addEventListener('change', validateFile);
    // });

    // // Validar tipo de archivo para múltiples cotizaciones
    // document.querySelector('input[name="nuevas_cotizaciones[]"]').addEventListener('change', validateMultipleFiles);

    // function validateFile(event) {
    //     const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    //     const file = event.target.files[0];
    //     if (file && !allowedExtensions.includes(file.name.split('.').pop().toLowerCase())) {
    //         Swal.fire('Archivo no compatible', 'Formato no permitido.', 'error');
    //         event.target.value = '';
    //     }
    // }

    // function validateMultipleFiles(event) {
    //     const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    //     Array.from(event.target.files).forEach(file => {
    //         if (!allowedExtensions.includes(file.name.split('.').pop().toLowerCase())) {
    //             Swal.fire('Archivo no compatible', 'Formato no permitido.', 'error');
    //             event.target.value = '';
    //         }
    //     });
    // }

    // document.querySelectorAll('.remove-file').forEach(button => {
    //     button.addEventListener('click', function () {
    //         const cotizacionId = this.dataset.id;
    //         Swal.fire({
    //             title: '¿Estás seguro?',
    //             text: 'El archivo será eliminado permanentemente.',
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonText: 'Sí, eliminar',
    //             cancelButtonText: 'Cancelar'
    //         }).then(result => {
    //             if (result.isConfirmed) {
    //                 fetch(`${APP_URL}/destroycoti/${cotizacionId}`, {
    //                     method: 'DELETE',
    //                     headers: {
    //                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //                     }
    //                 }).then(response => {
    //                     if (response.ok) {
    //                         Swal.fire('Eliminado', 'La cotización ha sido eliminada.', 'success');
    //                         location.reload();
    //                     } else {
    //                         Swal.fire('Error', 'No se pudo eliminar la cotización.', 'error');
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // });

     document.addEventListener('DOMContentLoaded', function () {
    // Validar tipo de archivo para diagnóstico
    document.getElementById('diagnostico').addEventListener('change', function (event) {
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        const file = event.target.files[0];

        if (file && !allowedExtensions.includes(file.name.split('.').pop().toLowerCase())) {
            Swal.fire('Archivo no compatible', 'Formato no permitido.', 'error');
            event.target.value = ''; // Vaciar input si el archivo no es válido
        }
    });
});

});
</script>

@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Editar Requisicion</span>
    </h4>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Solicita : {{ $requisicion->solicitante }}</h5>
                    <h5 class="mb-0 mx-3">Empresa : {{ $requisicion->rfc }}</h5>
                    <h5 class="mb-0 mx-3">Area : {{ $requisicion->area }}</h5>
                    <h5 class="mb-0">FOLIO: {{ $requisicion->folio }}</h5>
                     @if ($requisicion->idtipo_insumo == 1)
                        <h5 class="mb-0 mx-3">Tipo : <strong> Consumibles </strong></h5>
                    @else
                        <h5 class="mb-0 mx-3">Tipo : <strong> Especiales </strong></h5>
                    @endif

                </div>
                <hr class="my-0">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="formrequisicion" class="mb-3" action="{{ route('requisiciones-update', $requisicion->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="folio" value="{{ $requisicion->folio }}">
                        <input type="hidden" name="solicitante" value="{{ $requisicion->solicitante }}">
                        <input type="hidden" name="empresa" value="{{ $requisicion->empresa }}">
                        <input type="hidden" name="area" value="{{ $requisicion->area }}">

                       <div class="row">
                            <!-- <h5 class="mb-4">Editar Cotizaciones</h5>

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

                                <input type="file" class="form-control mt-2" name="nueva_cotizacion[{{ $cotizacion->id }}]">
                            </div>
                        @endforeach -->

                         <div class="col-md-4 mb-4" id="diagnosticoContainer" style="display: {{ $requisicion->idtipo_insumo == 2 ? 'block' : 'none' }};">
    <label for="diagnostico" class="form-label">Diagnóstico Actual</label>
    @if ($requisicion->diagnostico)
        <input type="text" class="form-control" value="{{ $requisicion->diagnostico }}" disabled>
        <a href="{{ route('download-cotizacion', ['file' => $requisicion->diagnostico]) }}" class="btn btn-sm btn-primary mx-2">Descargar</a>
    @endif

    <label for="diagnostico" class="form-label text-danger mt-2">Subir Nuevo Diagnóstico</label>
    <input type="file" name="diagnostico" id="diagnostico" class="form-control">
</div>
                        <!-- <div class="col-md-12 mb-3">
                            <label for="cotizaciones" class="form-label">Subir nuevas cotizaciones</label>
                            <input type="file" class="form-control" name="nuevas_cotizaciones[]" multiple>
                        </div> -->

                        <div id="rowsContainer">
                            @foreach ($productos as $producto)
                                <div class="row">
                                    <div class="col-md-1 mb-3">
                                        <label for="unidad" class="form-label">Unidad </label>
                                        <select class="form-select" name="unidad[]" required aria-label="select cantidad">
                                            <option value="">Selecciona cantidad </option>
                                            @foreach ($unidades as $unidad)
                                                <option value="{{ $unidad->clave }}" {{ $producto->unidad == $unidad->clave ? 'selected' : '' }}> {{ $unidad->clave }} </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('unidad'))
                                            <span class="text-danger">{{ $errors->first('unidad') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="descripcion" class="form-label">Descripción </label>
                                        <input type="text" class="form-control" name="descripcion[]" placeholder="Ingresa descripción" value="{{ $producto->descripcion }}" required>
                                        @if ($errors->has('descripcion'))
                                            <span class="text-danger">{{ $errors->first('descripcion') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-5 mb-3">
                                        <label for="justificacion" class="form-label">Justificación </label>
                                        <input type="text" class="form-control" name="justificacion[]" placeholder="Ingresa justificación" value="{{ $producto->justificacion }}" required>
                                        @if ($errors->has('justificacion'))
                                            <span class="text-danger">{{ $errors->first('justificacion') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-1 mb-3">
                                        <label for="cantidad" class="form-label">Cantidad </label>
                                        <input type="number" class="form-control" name="cantidad[]" value="{{ $producto->cantidad }}" required min="1">
                                        @if ($errors->has('cantidad'))
                                            <span class="text-danger">{{ $errors->first('cantidad') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-1 mt-4">
                                        <button type="button" class="btn btn-danger remove-row">-</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="addRowBtn" class="btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </div>
                    </form>

                    <template id="rowTemplate">
                        <div class="row">
                            <div class="col-md-1 mb-3">
                                <label class="form-label">Unidad </label>
                                <select class="form-select" name="unidad[]" required aria-label="select cantidad">
                                    <option value="">Selecciona cantidad </option>
                                    @foreach ($unidades as $unidad)
                                        <option value="{{$unidad->clave}}"> {{$unidad->clave}} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Descripción </label>
                                <input type="text" class="form-control" name="descripcion[]" placeholder="Ingresa descripción" required>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label">Justificación </label>
                                <input type="text" class="form-control" name="justificacion[]" placeholder="Ingresa justificación" required>
                            </div>

                            <div class="col-md-1 mb-3">
                                <label class="form-label">Cantidad </label>
                                <input type="number" class="form-control" name="cantidad[]" required min="1">
                            </div>

                            <div class="col-md-1 mt-4">
                                <button type="button" class="btn btn-danger remove-row">-</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection
