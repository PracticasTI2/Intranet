@extends('layouts/contentNavbarLayout')

@section('title', 'Requisiciones sin Cotizar')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <!-- Notifications -->
            <div class="d-flex justify-content-end m-2">
                <!-- <span class="">
                    <a href="{{ route('requisiciones-historial') }}" class="btn btn-info mx-4">Historial</a>
                </span>
                <span class="">
                    <a href="{{ route('requisiciones-create') }}" class="btn btn-primary">Agregar Requisicion</a>
                </span> -->
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
                        <form method="GET" action="{{ route('cotizaciones-index') }}" id="search-form">
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


                                <th class="text-center mt-2 pt-2">Acciones</th>
                            </tr>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($requis as $requisicion)
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


                                <td>
                                    <div class="d-flex ">
                                        <div class="text-center">

                                        <a type="button" class="btn btn-info me-4 p-1 "
                                            href="{{ route('cotizaciones-cotizar', $requisicion->id) }}">
                                            Subir Cotizaciones
                                        </a>

                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-body px-2 pt-4 pb-1">
                    {{ $requis->links('pagination::bootstrap-5') }}
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
            'input[name="empresa"], input[name="solicitante"], input[name="estatus"], input[name="fecha"], input[name="folio"]').forEach(
            function(input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        form.submit();
                    }
                });
            });

       
    </script>
@endsection
