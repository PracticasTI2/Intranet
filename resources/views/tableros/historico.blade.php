@extends('layouts/contentNavbarLayout')

@section('title', 'Histórico de Requisiciones')

@section('content')
<div class="col-md-12">
    <div class="card">
        <!-- Notifications -->
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
            @csrf <!-- Agregar el token CSRF -->
            <table class="table table-striped table-borderless border-bottom pb-4">
                <thead>
                    <form method="GET" action="{{ route('requisiciones-historico') }}" id="historico-form">
                        <tr>
                            <th class="mt-2 pt-2"># Folio</th>
                            <th class="mt-2 pt-2">Empresa
                                <div class="">
                                    <input type="text" name="empresa" placeholder="Empresa..." value="{{ request('empresa') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Solicitante
                                <div class="">
                                    <input type="text" name="solicitante" placeholder="Solicitante..." value="{{ request('solicitante') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Estatus
                                <div class="">
                                    <input type="text" name="estatus" placeholder="Estatus..." value="{{ request('estatus') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Fecha Solicitud
                                <div class="">
                                    <input type="date" name="fecha" placeholder="Fecha..." value="{{ request('fecha') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <!-- Puedes agregar más filtros si es necesario -->
                        </tr>
                    </form>
                </thead>
                <tbody>
                    @foreach ($requisiciones as $requisicion)
                    <tr>
                        <td>{{ $requisicion->folio }}</td>
                        <td>{{ $requisicion->empresas->rfc }}</td>
                        <td>{{ $requisicion->solicitante }}</td>
                        <td>
                            @if($requisicion->estado == 1)
                            <span class="p-2 text-warning rounded">Pendiente</span>
                            @elseif($requisicion->estado == 2)
                            <span class="p-2 text-success rounded">Autorizado</span>
                            @elseif($requisicion->estado == 3)
                            <span class="p-2 text-danger rounded">Rechazado</span>
                            @elseif($requisicion->estado == 4)
                            <span class="p-2 text-info rounded">Preautorizado</span>
                            @elseif($requisicion->estado == 5)
                            <span class="p-2 bg-secondary text-white rounded">Entregado</span>
                            @elseif($requisicion->estado == 6)
                                <span class="p-2 text-info rounded">Capturado</span>
                            @endif
                        </td>
                       <td>{{ \Carbon\Carbon::parse($requisicion->fechasol)->format('d/m/Y') }}</td>

                       <td> <a type="button" class="btn btn-info me-4 p-2" href="{{ route('requisiciones-show', $requisicion->id) }}">
                            Ver
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="card-body px-2 pt-4 pb-1">
                {{ $requisiciones->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
    // Este script maneja el envío del formulario al presionar Enter en los campos de filtro
    let form = document.querySelector('#historico-form');
    document.querySelectorAll('input[name="empresa"], input[name="solicitante"], input[name="estatus"], input[name="fecha"]').forEach(function (input) {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                form.submit();
            }
        });
    });
</script>
@endsection
