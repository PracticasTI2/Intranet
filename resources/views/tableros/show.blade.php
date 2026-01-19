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

                <h5 class="text-center text-uppercase"> EMPRESA:  {{ $requisiciones->rfc }}  <strong> FOLIO : {{ $requisiciones->folio }}</strong> </h5>

                <h5 class="text-center text-uppercase"> Solicita : {{ $requisiciones->solicitante }}  AREA : {{ $requisiciones->area }}</h5>

                <h5 class="text-center text-uppercase"> Fecha Solicitud : {{ \Carbon\Carbon::parse($requisiciones->fechasol)->format('d/m/Y') }}
                     Estado  :  @if($requisiciones->estado == 1)
                                    <strong> Pendiente </strong>
                                @elseif($requisiciones->estado == 2)
                                    <strong> Autorizado </strong>
                                @elseif($requisiciones->estado == 3)
                                    <strong> Rechazado </strong>
                                @elseif($requisiciones->estado == 4)
                                    <strong> Preautorizado </strong>
                                @elseif($requisiciones->estado == 5)
                                    <strong> Entredado </strong>
                                @endif
                </h5>
            </div>
            <div class="table-responsive m-2">
                <table class="table table-striped table-borderless border-bottom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Descripción</th>
                            <th>Justificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $index => $req)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                            <td>{{ $req->cantidad }}</td>
                            <td>{{ $req->unidad }}</td>
                            <td>{{ $req->descripcion }}</td>
                            <td>{{ $req->justificacion }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-12 text-end">
                    <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
