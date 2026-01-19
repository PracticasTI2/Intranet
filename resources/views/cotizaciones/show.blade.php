@extends('layouts/contentNavbarLayout')

@section('title', 'Detalles de Requisición')

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
                        @endif
                    </h5>

                    @if ($requisiciones->idtipo_insumo == 1)
                        <h5 class="text-center text-uppercase">Tipo : <strong> Consumibles </strong></h5>
                    @else
                        <h5 class="text-center text-uppercase">Tipo : <strong> Especiales </strong></h5>
                    @endif


                </div>
                <div class="table-responsive m-2">
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

                @if(!empty($cotizaciones) && count($cotizaciones) > 0)
                <div class="table-responsive mx-2 my-4">
                    <h6 class="text-center text-uppercase">Cotizaciones Guardadas</h6>
                    <table class="table table-striped table-borderless border-bottom p-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Archivo</th>
                                <th>Ver/Descargar</th>
                                @if (
                                    (in_array('contralor', $userRole) && $requisiciones->idtipo_insumo == 2 && $requisiciones->estado == 4) ||
                                        (in_array('autorizainsumos', $userRole) && $requisiciones->idtipo_insumo == 1 && $requisiciones->estado == 4) ||
                                        (in_array('encargado', $userRole) && $requisiciones->estado == 1))
                                    @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                                        <th>Selecciona</th>
                                    @endif
                                @endif
                            </tr>
                        </thead>
                        <tbody id="cotizaciones_body">
                            @foreach ($cotizaciones as $cotizacion)
                            <!-- Verifica si el usuario tiene el rol de "recursos_materiales" y si el estatus es 1 -->
                            @if (in_array('recursos_materiales', $userRole) && $cotizacion->estatus == 1)
                                <!-- Solo se mostrarán las cotizaciones con estatus = 1 para el rol "recursos_materiales" -->
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
                                    @if (
                                        (in_array('contralor', $userRole) && $requisiciones->idtipo_insumo == 2 && $requisiciones->estado == 4) ||
                                            (in_array('autorizainsumos', $userRole) && $requisiciones->idtipo_insumo == 1 && $requisiciones->estado == 4) ||
                                            (in_array('encargado', $userRole) && $requisiciones->estado == 1))
                                        @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                                            <td>
                                                <!-- Radio button para seleccionar solo una cotización -->
                                                <input type="radio" id="cotizacion_elegir" name="cotizacion_elegir"
                                                    value="{{ $cotizacion->id }}">
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @elseif (!in_array('recursos_materiales', $userRole))
                                <!-- Mostrar todas las cotizaciones para los demás roles -->
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
                                    @if (
                                        (in_array('contralor', $userRole) && $requisiciones->idtipo_insumo == 2 && $requisiciones->estado == 4) ||
                                            (in_array('autorizainsumos', $userRole) && $requisiciones->idtipo_insumo == 1 && $requisiciones->estado == 4) ||
                                            (in_array('encargado', $userRole) && $requisiciones->estado == 1))
                                        @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                                            <td>
                                                <!-- Radio button para seleccionar solo una cotización -->
                                                <input type="radio" id="cotizacion_elegir" name="cotizacion_elegir"
                                                    value="{{ $cotizacion->id }}">
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endif
                        @endforeach

                        </tbody>

                    </table>
                </div>
                @endif

                <div class="row m-2">
                    <div class="col-md-12">

                        <p>Respuesta Preautorizada : {{ $requisiciones->respuestapreautoriza }}</p>

                        <p>Respuesta Autorizada/Rechazada : {{ $requisiciones->respuesta }}</p>

                    </div>
                </div>
                <div class="row m-2 col-2">
                    @if (
                        (in_array('contralor', $userRole) && $requisiciones->idtipo_insumo == 2 && $requisiciones->estado == 4) ||
                            (in_array('autorizainsumos', $userRole) && $requisiciones->idtipo_insumo == 1 && $requisiciones->estado == 4) ||
                            (in_array('encargado', $userRole) && $requisiciones->estado == 1))
                        @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                            <a type="button" class="btn btn-primary text-white me-4 p-1" data-bs-toggle="modal"
                                data-bs-target="#modalAutorizar" onclick="setRequisicionId('{{ $id_requi }}')">
                                Autorizar / Rechazar
                            </a>
                        @else
                            <a type="button" class="btn btn-primary text-white me-4 p-1" data-bs-toggle="modal"
                                data-bs-target="#modalAutorizar" onclick="setRequisicionId('{{ $id_requi }}')">
                                Preautorizar / Rechazar
                            </a>
                        @endif
                    @endif

                    @if ($requisiciones->estado == 6 && $requisiciones->id_solicitante == $usuarioLogueadoId)
                        <a type="button" class="btn btn-warning m-0 p-1 me-4"
                            href="{{ route('requisiciones-edit', $id_requi) }}">
                            Editar
                        </a>
                    @endif

                    {{-- @if ($requisiciones->estado == 6)
                    <a type="button" class="btn btn-success text-white me-4 p-1"
                        onclick="setEnviadoId('{{ $requisiciones->id }}')">
                        Enviar
                    </a>
                @endif --}}

                 <!-- @if ($requisiciones->estado == 5) {{-- Estado "Entregado" --}}
                        <a id="btncosto" name="btncosto" class="btn  btn-outline-secondary me-4 p-1" href="">
                            editar costo

                        </a>
                @endif -->

                    @can('descargar-pdf')
                        @if ($requisiciones->estado == 2)
                            <a class="btn btn-dark p-1 me-4" href="{{ route('requisicion.pdf', $requisiciones->id) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512">
                                    <path fill="#f3f4f7"
                                        d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                                </svg>
                            </a>
                        @endif
                    @endcan
                </div>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                    </div>
                </div>
            </div>




            <!-- Modal -->
            <div class="modal fade" id="modalAutorizar" tabindex="-1"
                aria-labelledby="exampleModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Autorizar/Rechazar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAutorizar" action="{{ route('requisiciones-autorizar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" id="requisicionId" value="{{ old('id') }}">
                                {{-- @error('id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}

                                <input type="hidden" name="id_cotizacion_aceptada" id="id_cotizacion_aceptada"
                                    value="{{ old('id_cotizacion_aceptada') }}">
                                {{-- @error('id_cotizacion_aceptada')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" name="estado" id="estado" required>
                                        <option value="" selected disabled>Seleccione una opcion
                                        </option>

                                        @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                                            <option value="2" {{ old('estado') == '2' ? 'selected' : '' }}>Autorizado
                                            </option>
                                        @endif

                                        @if (in_array('encargado', $userRole))
                                            <option value="4" {{ old('estado') == '4' ? 'selected' : '' }}>
                                                Preautorizado</option>
                                        @endif

                                        <option value="3" {{ old('estado') == '3' ? 'selected' : '' }}>Rechazado
                                        </option>
                                    </select>
                                    {{-- @error('estado')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror --}}
                                </div>

                                <div class="mb-3">
                                    <label for="respuesta" class="form-label">Respuesta</label>
                                    <textarea class="form-control" name="respuesta" id="respuesta" rows="3" required>{{ old('respuesta') }}</textarea>
                                    {{-- @error('respuesta')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror --}}
                                </div>

                                <input type="hidden" name="fechares" id="fechares" value="{{ old('fechares') }}">
                                {{-- @error('fechares')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}

                                <input type="hidden" name="semanres" id="semanres" value="{{ old('semanres') }}">
                                {{-- @error('semanres')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}

                                <input type="hidden" name="id_userautoriza" value="{{ Auth::user()->id }}">
                                {{-- @error('id_userautoriza')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror --}}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary"
                                onclick="submitAutorizarForm()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function setRequisicionId(id) {

            let cotizacion = document.querySelector('input[name="cotizacion_elegir"]:checked');
            var requi_elegida;

          const tbody = document.getElementById('cotizaciones_body');
            if (tbody && tbody.rows.length > 0) {
                if (cotizacion == null) {
                    requi_elegida = null;
                } else {
                    requi_elegida = cotizacion.value;
                }
            } else {
                requi_elegida = 'aceptada';
            }

            document.getElementById('id_cotizacion_aceptada').value = requi_elegida;
            document.getElementById('requisicionId').value = id;




            const fechaActual = new Date();
            const yyyy = fechaActual.getFullYear();
            const mm = String(fechaActual.getMonth() + 1).padStart(2, '0');
            const dd = String(fechaActual.getDate()).padStart(2, '0');

            // Set fechares
            document.getElementById('fechares').value = `${yyyy}-${mm}-${dd}`;

            // Calculate week number
            const firstDayOfYear = new Date(fechaActual.getFullYear(), 0, 1);
            const pastDaysOfYear = (fechaActual - firstDayOfYear) / 86400000;
            const weekNumber = Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);

            // Set semanres
            document.getElementById('semanres').value = weekNumber;
        }

        function submitAutorizarForm() {
            document.getElementById('formAutorizar').submit();
        }

        function setEntregadoId(id) {
            document.getElementById('entregadoId').value = id;

            Swal.fire({
                title: "¿Estás seguro?",
                text: "No se podrá revertir tu elección.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, seguro",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formEntregado').submit();
                }
            });
        }

        function setEnviadoId(id) {
            document.getElementById('pendienteId').value = id;
            Swal.fire({
                title: "¿Estás seguro?",
                text: "No se podrá revertir tu elección.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, seguro",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formPendiente').submit();
                }
            });
        }
    </script>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Construir la lista de errores en formato HTML
        let errores = '<ul style="text-align: left;">';
        @foreach ($errors->all() as $error)
            errores += '<li>{{ $error }}</li>';
        @endforeach
        errores += '</ul>';

        // Usar SweetAlert para mostrar los errores con formato HTML y alineación a la izquierda
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores,
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'text-start'
            }
        }).then((result) => {
            // Verificar si se presionó el botón de confirmación
            if (result.isConfirmed) {
                Swal.close(); // Asegura que el modal se cierre
            }
        });
    });
</script>
@endif

@endsection
