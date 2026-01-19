@extends('layouts/contentNavbarLayout')

@section('title', 'Historial')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <!-- Notifications -->
            <div class="d-flex justify-content-end m-2">

                <div class="col-md-12 text-end">
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
                        <form method="GET" action="{{ route('requisiciones-historial') }}" id="search-form">
                            <tr>
                                <th class="mt-2 pt-2"># Folio</th>

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



                                        <a type="button" class="btn btn-info me-4 p-1"
                                            href="{{ route('requisiciones-show', $requisicion->id) }}">
                                            Ver
                                        </a>

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





    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let form = document.querySelector('#search-form');
        document.querySelectorAll(
            'input[name="empresa"], input[name="solicitante"], input[name="estatus"], input[name="fecha"]').forEach(
            function(input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        form.submit();
                    }
                });
            });



    </script>
@endsection
