@extends('layouts/contentNavbarLayout')

@section('title', 'Lista de Requisiciones')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <!-- Notifications -->
            <div class="d-flex justify-content-end m-2">
                <span class="">
                    <a href="{{ route('requisiciones-historial') }}" class="btn btn-info mx-4">Historial</a>
                </span>
                <span class="">
                    <a href="{{ route('requisiciones-create') }}" class="btn btn-primary">Agregar Requisicion</a>
                </span>
            </div>

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
            <div class="table-responsive">
                @csrf
                <table class="table table-striped table-borderless border-bottom pb-4">
                    <thead>
                        <form method="GET" action="{{ route('requisiciones-index') }}" id="search-form">
                            <tr>
                                <th class="mt-2 pt-2">Folio
                                    <div class="">
                                        <input type="text" name="folio" placeholder="folio..."
                                            value="{{ request('folio') }}" autocomplete="off"
                                            class="form-control text-xs mt-2" />
                                    </div>
                                </th>

                                <th class="mt-2 pt-2">Solicitante
                                    <div class="">
                                        <input type="text" name="solicitante" placeholder="solicitante..."
                                            value="{{ request('solicitante') }}" autocomplete="off"
                                            class="form-control text-xs mt-2" />
                                    </div>
                                </th>
                                <th class="mt-2 pt-2">Estatus
                                    <div class="">
                                        <input type="text" name="estatus" placeholder="estatus..."
                                            value="{{ request('estatus') }}" autocomplete="off"
                                            class="form-control text-xs mt-2" />
                                    </div>
                                </th>
                                <th class="mt-2 pt-2">Fecha Solicitud
                                    <div class="">
                                        <input type="date" name="fecha" placeholder="Fecha..."
                                            value="{{ request('fecha') }}" autocomplete="off"
                                            class="form-control text-xs mt-2" />
                                    </div>
                                </th>
                                <th class="mt-2 pt-2">Tipo de Insumo

                                </th>
                                <th class="text-center mt-2 pt-2">Acciones</th>
                            </tr>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($requisiciones as $requisicion)
                            <tr>
                                <td>{{ $requisicion->folio }}</td>

                                <td>{{ $requisicion->solicitante }}</td>
                                <td>
                                    @if ($requisicion->estado == 1)
                                        <span class="p-2 text-warning rounded">Pendiente</span>
                                    @elseif ($requisicion->estado == 2)
                                        <span class="p-2 text-success rounded">Autorizado</span>
                                    @elseif ($requisicion->estado == 3)
                                        <span class="p-2 text-danger rounded">Rechazado</span>
                                    @elseif ($requisicion->estado == 4)
                                        <span class="p-2 text-info rounded">Preautorizado</span>
                                    @elseif ($requisicion->estado == 5)
                                        <span class="p-2 text-secondary rounded">Entregado</span>
                                    @elseif ($requisicion->estado == 6)
                                        <span class="p-2 text-primary rounded">Capturado</span>
                                    @elseif ($requisicion->estado == 7)
                                        <span class="p-2 text-warning rounded">Cotizado</span>
                                    @elseif ($requisicion->estado == 8)
                                        <span class="p-2 text-success rounded">Autorizado No Cotizar</span>
                                    @elseif ($requisicion->estado == 9)
                                        <span class="p-2 text-primary rounded">Autorizado Necesita Cotización</span>
                                    @elseif ($requisicion->estado == 10)
                                        <span class="p-2 text-success rounded">Liberado</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($requisicion->fechasol)->format('d/m/Y') }}</td>
                                <td>
                                    @if ($requisicion->idtipo_insumo == 1)
                                        <span class="p-2 text-warning rounded">Consumibles</span>
                                    @else
                                        <span class="p-2 text-info rounded">Especiales</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex">
                                        {{-- @if ((in_array('contralor', $userRole) && $requisicion->idtipo_insumo == 2 && $requisicion->estado == 4) || (in_array('autorizainsumos', $userRole) && $requisicion->idtipo_insumo == 1 && $requisicion->estado == 4) || (in_array('encargado', $userRole) && $requisicion->estado == 1))
                                            @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole))
                                                <a type="button" class="btn btn-primary text-white me-4 p-1"
                                                    data-bs-toggle="modal" data-bs-target="#modalAutorizar"
                                                    onclick="setRequisicionId('{{ $requisicion->id }}')">
                                                    Autorizar
                                                </a>
                                            @else
                                                <a type="button" class="btn btn-primary text-white me-4 p-1"
                                                    data-bs-toggle="modal" data-bs-target="#modalAutorizar"
                                                    onclick="setRequisicionId('{{ $requisicion->id }}')">
                                                    Preautorizar
                                                </a>
                                            @endif
                                        @endif --}}

                                        @if ($requisicion->estado == 6 && $requisicion->id_solicitante == $usuarioLogueadoId)
                                            <a type="button" class="btn btn-warning m-0 p-1 me-4"
                                                href="{{ route('requisiciones-edit', $requisicion->id) }}">
                                                Editar
                                            </a>
                                        @endif

                                        @if ($requisicion->estado == 5)
                                            {{-- Estado "Entregado" --}}
                                            <a class="btn  btn-outline-secondary me-4 p-1 position-relative"
                                                href="{{ route('requisiciones-duplicar', $requisicion->id) }}">
                                                Pedir de Nuevo
                                                <i class='bx bxs-star position-absolute top-0 start-100 translate-middle '
                                                    style='color:#e41919'></i>
                                            </a>
                                        @endif

                                        @if ($requisicion->estado == 3)
                                            {{-- Estado "Rechazado" --}}
                                            <a class="btn  btn-outline-warning me-4 p-1 position-relative"
                                                href="{{ route('requisiciones-duplicar', $requisicion->id) }}">
                                                Corregir

                                            </a>
                                        @endif

                                        <a type="button" class="btn btn-info me-4 p-1 "
                                            href="{{ route('requisiciones-show', $requisicion->id) }}">
                                            Ver
                                        </a>

                                        @if (in_array('recursos_materiales', $userRole) && $requisicion->estado == 2)
                                            <a type="button" class="btn btn-secondary text-white me-4 p-1"
                                                onclick="setEntregadoId('{{ $requisicion->id }}')">
                                                Entregado
                                            </a>
                                        @endif

                                        @if ($requisicion->estado == 6)
                                            <a type="button" class="btn btn-success text-white me-4 p-1"
                                                onclick="setEnviadoId('{{ $requisicion->id }}')">
                                                Enviar
                                            </a>
                                        @endif

                                        @can('descargar-pdf')
                                            @if ($requisicion->estado == 2)
                                                <a class="btn btn-dark p-1 me-4"
                                                    href="{{ route('requisicion.pdf', $requisicion->id) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                                                        viewBox="0 0 512 512">
                                                        <path fill="#f3f4f7"
                                                            d="M64 464l48 0 0 48-48 0c-35.3 0-64-28.7-64-64L0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 304l-48 0 0-144-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-body px-2 pt-4 pb-1">
                    {{ $requisiciones->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalAutorizar" tabindex="-1" aria-labelledby="exampleModalLabel">
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
                                @error('id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

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
                                    @error('estado')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="respuesta" class="form-label">Respuesta</label>
                                    <textarea class="form-control" name="respuesta" id="respuesta" rows="3" required>{{ old('respuesta') }}</textarea>
                                    @error('respuesta')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="fechares" id="fechares" value="{{ old('fechares') }}">
                                @error('fechares')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                                <input type="hidden" name="semanres" id="semanres" value="{{ old('semanres') }}">
                                @error('semanres')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                                <input type="hidden" name="id_userautoriza" value="{{ Auth::user()->id }}">
                                @error('id_userautoriza')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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

    <form id="formEntregado" action="{{ route('requisiciones-entregado') }}" method="post">
        @csrf
        <input type="hidden" name="id_e" id="entregadoId" value="{{ old('id') }}">
        <input type="hidden" name="estado_entregado" value="5">
    </form>

    <form id="formPendiente" action="{{ route('requisiciones-pendiente') }}" method="post">
        @csrf
        <input type="hidden" name="id_p" id="pendienteId" value="{{ old('id') }}">
        <input type="hidden" name="estado_pendiente" value="1">
    </form>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let form = document.querySelector('#search-form');
        document.querySelectorAll(
            'input[name="empresa"], input[name="solicitante"], input[name="estatus"], input[name="fecha"], input[name="folio"]'
            ).forEach(
            function(input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        form.submit();
                    }
                });
            });

        function setRequisicionId(id) {
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
@endsection
