@extends('layouts/contentNavbarLayout')

@section('title', 'Duplicar Requisición')

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#cotizaciones').on('change', function() {
            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'png'];
            const files = this.files;
            let invalidFile = false;

            for (let i = 0; i < files.length; i++) {
                const extension = files[i].name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    invalidFile = true;
                    break;
                }
            }

            if (invalidFile) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten archivos PDF, DOC, JPG o PNG.',
                    confirmButtonText: 'OK'
                });
                this.value = '';
            }
        });

        $('#formDuplicar').on('submit', function(e) {
    // Verificar que haya al menos una fila.
    if ($('#rowsContainer .row').length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe agregar al menos un producto.',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Deshabilitar los inputs de filas eliminadas para que no se envíen.
    $('#rowsContainer .row:hidden input, #rowsContainer .row:hidden select').prop('disabled', true);
});

        $('#addRowBtn').on('click', function() {
            const template = document.getElementById('rowTemplate').content.cloneNode(true);
            $('#rowsContainer').append(template);
        });

       $('#rowsContainer').on('click', '.remove-row', function() {
    $(this).closest('.row').remove(); // Eliminar la fila del DOM.
});
    });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">Solicitar de Nuevo</h4>

<div class="card mb-4">
    <div class="card-header">
        <h5>Solicitante: {{ $usuario->usuario_nombre }}</h5>
        <h5>Área: {{ $usuario->area_nombre }}</h5>
    </div>
    <div class="card-body">
        <form id="formDuplicar" action="{{ route('requisiciones-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="solicitante" value="{{ $usuario->usuario_nombre }}">
            <input type="hidden" name="area" value="{{ $usuario->area_nombre }}">

            <div class="row">
                <div class="col-md-4" >
                    <label for="insumo" class="form-label">Tipo de Insumo</label>
                    <select name="insumo" class="form-select"  >

                        @foreach ($tipoinsumo as $insumo)
                            <option value="{{ $insumo->idtipo_insumo }}"   {{ $insumo->idtipo_insumo == $requisicionOriginal->idtipo_insumo ? 'selected' : '' }} >
                                {{ $insumo->nombre_insumo }}
                            </option>



                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cotizaciones</label>
                    <input type="file" name="cotizaciones[]" id="cotizaciones" class="form-control" multiple>
                </div>
            </div>

            <div id="rowsContainer" class="mt-3">
                @foreach ($productosOriginales as $producto)
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <select name="unidad[]" class="form-select" required>

                                @foreach ($unidades as $unidad)
                                    <option value="{{ $unidad->clave }}"
                                        {{ $unidad->clave == $producto->unidad ? 'selected' : '' }}>
                                        {{ $unidad->clave }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="descripcion[]" class="form-control"
                                   value="{{ $producto->descripcion }}" placeholder="Descripción" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="justificacion[]" class="form-control"
                                   value="{{ $producto->justificacion }}" placeholder="Justificación" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="cantidad[]" class="form-control"
                                   value="{{ $producto->cantidad }}" min="1" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-row">Borrar</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" id="addRowBtn" class="btn btn-primary mt-3">Agregar Producto</button>

            <div class="mt-4 text-end">
                <button type="button" class="btn btn-secondary" onclick="history.back()">Atrás</button>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>

        <template id="rowTemplate">
            <div class="row mb-2">
                <div class="col-md-2">
                    <select name="unidad[]" class="form-select" required>

                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->clave }}">{{ $unidad->clave }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="descripcion[]" class="form-control" placeholder="Descripción" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="justificacion[]" class="form-control" placeholder="Justificación" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="cantidad[]" class="form-control" min="1" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">Borrar</button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
