@extends('layouts/contentNavbarLayout')

@section('title', 'Agregar- Requisicion')

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


});
</script>
    <script>
         function validarCantidad(input) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }
       $(document).ready(function() {
            // SweetAlert para mensajes de sesión
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('requisiciones-index') }}";
                    }
                });
            @elseif(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('requisiciones-index') }}";
                    }
                });
            @endif

            // Funcionalidad de agregar y remover filas
            const addRowBtn = document.getElementById('addRowBtn');
            const rowsContainer = document.getElementById('rowsContainer');
            const template = document.getElementById('rowTemplate');

            addRowBtn.addEventListener('click', function() {
                const newRow = template.content.cloneNode(true);
                rowsContainer.appendChild(newRow);
            });

            rowsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('.row').remove();
                }
            });

            // Agregar inputs antiguos
            @if (old('unidad'))
                @foreach (old('unidad') as $index => $unidad)
                    const newRow = template.content.cloneNode(true);
                    newRow.querySelector('[name="unidad[]"]').value = "{{ old('unidad.'.$index) }}";
                    newRow.querySelector('[name="descripcion[]"]').value = "{{ old('descripcion.'.$index) }}";
                    newRow.querySelector('[name="justificacion[]"]').value = "{{ old('justificacion.'.$index) }}";
                    newRow.querySelector('[name="cantidad[]"]').value = "{{ old('cantidad.'.$index) }}";
                    rowsContainer.appendChild(newRow);
                @endforeach
            @endif

            // Validar cantidad

        });

            document.getElementById('formrequisicion').addEventListener('submit', function(event) {
            const rowsContainer = document.getElementById('rowsContainer');
            const rows = rowsContainer.getElementsByClassName('row');

            if (rows.length === 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe agregar al menos un artículo antes de guardar.',
                    confirmButtonText: 'OK'
                });
            }
        });

    </script>


<script>
    $(document).ready(function() {
        // Mostrar u ocultar el campo de diagnóstico dependiendo del valor seleccionado
        $('#insumo').on('change', function() {
            const selectedValue = $(this).val();
            const diagnosticoContainer = $('#diagnosticoContainer');

            // Aquí verifica cuál opción de insumo necesita mostrar el input de diagnóstico
            if (selectedValue == 2) {
                diagnosticoContainer.show();  // Muestra el input para diagnóstico
            } else {
                diagnosticoContainer.hide();  // Oculta el input para diagnóstico
            }
        });

        // Ejecutar la función al cargar la página para que se ajuste según la opción seleccionada
        const initialValue = $('#insumo').val();
        if (initialValue == 2) {
            $('#diagnosticoContainer').show();
        } else {
            $('#diagnosticoContainer').hide();
        }
    });
</script>

@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Agregar Requisicion</span>
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
                    <h4 class="mx-5 mb-0">Solicita : {{ Auth::user()->name }}</h4>
                    <h4 class="mb-0 mx-3">Area : {{ $usuario->nombre }}</h4>
                    <h4 class="mb-0 mx-3">{{ $empresas->rfc }}</h4>



                </div>
                <hr class="my-0">
                    <div class="card-body">
                        <form id="formrequisicion" class="mb-3" action="{{ route('requisiciones-store') }}" method="post"  enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="solicitante" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="area" value="{{ $usuario->nombre }}">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label for="insumo" class="form-label">Tipo de insumo </label>
                                    <select class="form-select"  name="insumo" id="insumo" required aria-label="select insumo">
                                        <option value="" disabled selected>Selecciona tipo de insumo</option>
                                        @foreach ($tipoinsumo as $insumo)
                                            <option value="{{$insumo->idtipo_insumo}}" {{ old('insumo') == $insumo->idtipo_insumo ? 'selected' : '' }}> {{$insumo->nombre_insumo}} </option>
                                        @endforeach
                                    </select>
                                    @error('insumo')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-4" id="diagnosticoContainer" style="display: none;">
                                    <label for="diagnostico" class="form-label text-danger">Subir Diagnóstico</label>
                                    <input type="file" name="diagnostico" id="diagnostico" class="form-control">
                                </div>
                                <!-- <div class="col-md-4 mb-4">
                                    <label for="" class="form-label text-danger">Selecciona todas las cotizaciones que deseas subir</label>
                                       <label for="cotizaciones" class="form-label">Cotizaciones</label>
                                        <input type="file" name="cotizaciones[]" id="cotizaciones" class="form-control" multiple>
                                </div> -->

                            </div>

                            <div id="rowsContainer">
                                @if(old('unidad'))
                                    @foreach(old('unidad') as $index => $unidad)
                                        <div class="row">
                                            <div class="col-md-1 mb-3">
                                                <label for="unidad" class="form-label">Unidad </label>
                                                <select class="form-select" name="unidad[]" required aria-label="select cantidad" autofocus>
                                                    <option value="">Selecciona cantidad</option>
                                                    @foreach ($unidades as $u)
                                                        <option value="{{ $u->clave }}" {{ old('unidad.' . $index) == $u->clave ? 'selected' : '' }}>{{ $u->clave }}</option>
                                                    @endforeach
                                                </select>
                                                @error('unidad.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="descripcion" class="form-label">Descripción </label>
                                                <input type="text" class="form-control" name="descripcion[]" value="{{ old('descripcion.' . $index) }}" placeholder="Ingresa descripción" required >
                                                @error('descripcion.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label for="justificacion" class="form-label">Justificación </label>
                                                <input type="text" class="form-control" name="justificacion[]" value="{{ old('justificacion.' . $index) }}" placeholder="Ingresa justificación" required >
                                                @error('justificacion.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 mb-3">
                                                <label for="cantidad" class="form-label">Cantidad </label>
                                                <input type="number" class="form-control" name="cantidad[]" value="{{ old('cantidad.' . $index) }}" required autofocus>
                                                @error('cantidad.' . $index)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 mt-4">
                                                <button type="button" class="btn btn-danger remove-row">Borrar</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <a type="button" id="addRowBtn" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24" width="21" viewBox="0 0 448 512">
                                            <path fill="#f0f2f4" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 text-end">
                                    <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                                    <button type="submit" class="btn btn-success">Guardar Borrador</button>
                                </div>
                            </div>
                        </form>
                        <template id="rowTemplate">
                            <div class="row">
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Unidad </label>
                                    <select class="form-select" name="unidad[]" required aria-label="select unidad" autofocus>
                                        <option value="">Selecciona cantidad</option>
                                        @foreach ($unidades as $unidad)
                                            <option value="{{$unidad->clave}}"> {{$unidad->clave}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Descripción </label>
                                    <input type="text" class="form-control" name="descripcion[]" placeholder="Ingresa descripción" required >
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Justificación </label>
                                    <input type="text" class="form-control" name="justificacion[]" placeholder="Ingresa justificación" required >
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">Cantidad </label>
                                    <input type="number" class="form-control" name="cantidad[]" min="1" required oninput="validarCantidad(this)">
                                </div>
                                <div class="col-md-1 mt-4">
                                    <button type="button" class="btn btn-danger remove-row">Borrar</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
