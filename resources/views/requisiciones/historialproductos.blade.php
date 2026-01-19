@extends('layouts/contentNavbarLayout')

@section('title', 'Historial Pedidos')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <!-- Notifications -->
            <div class="d-flex justify-content-end m-2">

                <div class="col-md-12 text-end">
                    <a class="btn btn-success p-2 m-2 text-white" id="excel" data-data='@json($productosreq)'
                        onclick="enviarExcel()">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 384 512">
                            <path fill="#f3f4f7"
                                d="M48 448L48 64c0-8.8 7.2-16 16-16l160 0 0 80c0 17.7 14.3 32 32 32l80 0 0 288c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16zM64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-293.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0L64 0zm90.9 233.3c-8.1-10.5-23.2-12.3-33.7-4.2s-12.3 23.2-4.2 33.7L161.6 320l-44.5 57.3c-8.1 10.5-6.3 25.5 4.2 33.7s25.5 6.3 33.7-4.2L192 359.1l37.1 47.6c8.1 10.5 23.2 12.3 33.7 4.2s12.3-23.2 4.2-33.7L222.4 320l44.5-57.3c8.1-10.5 6.3-25.5-4.2-33.7s-25.5-6.3-33.7 4.2L192 280.9l-37.1-47.6z" />
                        </svg>
                        EXCEL
                    </a>
                    <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                </div>

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
                        <form method="GET" action="{{ route('requisiciones-historialproductos') }}" id="search-form">
                            <tr>
                                <th class="mt-2 pt-2"># Folio</th>

                                <th class="mt-2 pt-2">Descripción
                                    <div class="">
                                        <input type="text" name="descripcion" placeholder="descripcion..."
                                            value="{{ request('descripcion') }}" autocomplete="off"
                                            class="form-control text-xs mt-2" />
                                    </div>
                                </th>
                                <th class="mt-2 pt-2">Justificación
                                    <div class="">
                                        <input type="text" name="justificacion" placeholder="justificacion..."
                                            value="{{ request('justificacion') }}" autocomplete="off"
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
                                <th class="mt-2 pt-2">Área
                                    <div class="">
                                        <input type="text" name="area" placeholder="área..."
                                            value="{{ request('area') }}" autocomplete="off"
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
                                    <div class="row">
                                        <div class="col-6">(Desde...)
                                            <input type="date" name="inicio" placeholder="Fecha inicio..."
                                                value="{{ request('inicio') }}" autocomplete="off"
                                                class="form-control text-xs mt-2" />
                                        </div>
                                        <div class="col-6">(Hasta...)
                                            <input type="date" name="fin" placeholder="Fecha fin..."
                                                value="{{ request('fin') }}" autocomplete="off"
                                                class="form-control text-xs mt-2" />
                                        </div>
                                    </div>
                                </th>

                                <th class="mt-2 pt-2">Tipo de Insumo

                                </th>

                                <th class="mt-2 pt-2">Cantidad

                                </th>
                                <th class="text-center mt-2 pt-2">Acciones</th>
                            </tr>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($productosreq as $requisicion)
                            <tr>
                                <td>{{ $requisicion->folio }}</td>

                                <td>{{ $requisicion->descripcion }}</td>
                                <td>{{ $requisicion->justificacion }}</td>

                                <td>{{ $requisicion->solicitante }}</td>

                                <td>{{ $requisicion->area }}</td>

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
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($requisicion->fechasol)->format('d/m/Y') }}</td>
                                <td>
                                    @if ($requisicion->idtipo_insumo == 1)
                                        <span class="p-2 text-warning rounded">Consumibles</span>
                                    @else
                                        <span class="p-2 text-info rounded">Especiales</span>
                                    @endif
                                </td>

                                <td>{{ $requisicion->cantidad }}</td>

                                <td>
                                    <div class="d-flex">
                                        <a type="button" class="btn btn-info "
                                            href="{{ route('requisiciones-show', $requisicion->id) }}">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6"> </td>
                            <td>Total </td>
                            <td> {{ $total_productos['cantidad_total'] }} </td>
                        </tr>
                    </tbody>
                </table>

                <div class="card-body px-2 pt-4 pb-1">
                    {{ $productosreq->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                let form = document.querySelector('#search-form');
                document.querySelectorAll(
                    'input[name="empresa"], input[name="descripcion"], input[name="solicitante"], input[name="area"], input[name="justificacion"], input[name="estatus"], input[name="inicio"], input[name="fin"]'
                ).forEach(
                    function(input) {
                        input.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                form.submit();
                            }
                        });
                    });
            </script>

            <script>
                const exportExcelUrl = "{{ route('exportar.excel') }}";

                function enviarExcel() {
                    // Obtén los datos de productos filtrados y el total de productos desde variables PHP
                    const productosFiltrados = @json($productosfiltrados);
                    const totalProductos = @json($total_productos);

                    // Log de los productos filtrados para depuración
                    //console.log(productosFiltrados);

                    // Realiza la solicitud para exportar a Excel
                    fetch(exportExcelUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                productosFiltrados,
                                totalProductos
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            // Crear una URL de objeto desde el blob
                            const url = window.URL.createObjectURL(blob);

                            // Crear un enlace temporal para simular el clic y la descarga
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'reporterecursos.xlsx'; // Cambiar el nombre del archivo si es necesario
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);

                            // Liberar la URL del objeto
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(error => {
                            console.error('Error en la solicitud:', error);
                        });
                }
            </script>
        @endsection
