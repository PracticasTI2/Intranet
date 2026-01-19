@extends('layouts/contentNavbarLayout')

@section('title', 'Lista de Registros')

@section('content')
<div class="col-md-12">
    <div class="card">
        <!-- Notifications -->
        <div class="d-flex justify-content-end m-2">

            <span class="">
                <a href="{{ route('tableros-create') }}" class="btn btn-primary">Agregar Registro</a>
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
                    <form method="GET" action="{{ route('tableros-index') }}" id="search-form">
                        <tr>

                            <th class="mt-2 pt-2">Titulo
                                <div class="">
                                    <input type="text" name="titulo" placeholder="titulo..." value="{{ request('titulo') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Tipo Tablero
                                <div class="">
                                    <input type="text" name="tipotab" placeholder="tipotab..." value="{{ request('tipotab') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Descripción
                                <div class="">
                                    <input type="text" name="descripcion" placeholder="descripcion..." value="{{ request('descripcion') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Tiempo
                                <div class="">
                                    <input type="text" name="tiempo" placeholder="tiempo..." value="{{ request('tiempo') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Tipo
                                <div class="">
                                    <input type="text" name="tipo" placeholder="tipo..." value="{{ request('tipo') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>

                            <!-- <th class="mt-2 pt-2">Publicacion
                                <div class="">
                                    <input type="date" name="publicacion" placeholder="publicacion..." value="{{ request('publicacion') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th> -->

                            <th class="mt-2 pt-2">Inicio
                                <div class="">
                                    <input type="date" name="inicio" placeholder="inicio..." value="{{ request('inicio') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="mt-2 pt-2">Termino
                                <div class="">
                                    <input type="date" name="termino" placeholder="termino..." value="{{ request('termino') }}" autocomplete="off" class="form-control text-xs mt-2" />
                                </div>
                            </th>
                            <th class="text-center mt-2 pt-2">Acciones</th>
                        </tr>
                    </form>
                </thead>
                <tbody>
                    @foreach ($tableros as $tab)
                        <tr>

                            <td>{{ $tab->titulo }}</td>

                            <td>{{ $tab->tipo_tablero->nombre }}</td>
                            <td>{{ $tab->descripcion }}</td>
                            <td>{{ $tab->tiempo }}</td>
                            <td>{{ $tab->tipo }}</td>

                            <!-- <td>{{ \Carbon\Carbon::parse($tab->publicacion)->format('d/m/Y') }}</td> -->
                            <td>{{ \Carbon\Carbon::parse($tab->inicio)->format('d/m/Y') }}</td>

                            <td>{{ \Carbon\Carbon::parse($tab->termino)->format('d/m/Y') }}</td>



                            <td>
                                <div class="d-flex">
                                    <span class="d-inline m-2" style="white-space: nowrap;">
                                        <button class="btn btn-danger text-white" onclick="confirmDelete('{{ $tab->titulo }}', {{ $tab->idnota }})">
                                        <i class="bx bxs-trash text-white"></i>
                                        </button>
                                    </span>

                                    <span class="d-inline m-2" style="white-space: nowrap;">
                                        <a href="{{ route('tableros-edit', $tab->idnota) }}" class="btn btn-info text-white">
                                            <i class="bx bxs-edit text-white"></i>
                                        </a>
                                    </span>

                                </div>

                            </td>




                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="card-body px-2 pt-4 pb-1">
                {{ $tableros->links('pagination::bootstrap-5') }}
            </div>
        </div>


    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let form = document.querySelector('#search-form');
document.querySelectorAll('input[name="titulo"], input[name="tipotab"], input[name="descripcion"], input[name="tiempo"] , input[name="tipo"] , input[name="publicacion"] , input[name="inicio"],  input[name="termino"]').forEach(function (input) {
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            form.submit();
        }
    });
});

</script>

 <script>
    function confirmDelete(titulo, id) {
        var confirmMessage = "¿Estás seguro de que deseas eliminar a " + titulo + "?";
        if (confirm(confirmMessage)) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('tableros-destroy', ':id') }}'.replace(':id', id);
            form.style.display = 'none';

            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            var deleteMethod = document.createElement('input');
            deleteMethod.type = 'hidden';
            deleteMethod.name = '_method';
            deleteMethod.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(deleteMethod);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
