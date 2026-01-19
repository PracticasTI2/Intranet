@extends('layouts/contentNavbarLayout')

@section('title', 'Listar Areas')


@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="row">

    <h4 class="fw-bold py-3 mb-2">
        <span class="text-muted ">Areas</span>
    </h4>

    <div class="row">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif
    </div>

    <div class="col-md-12">
        <div class="card">
            <!-- Notifications -->
            <div class="d-flex justify-content-end m-2">
                <span class="">
                    <a href="{{route('areas-create')}}" class="btn btn-primary">Agregar Area</a>
                </span>
            </div>

            <div class="table-responsive ">
                @csrf <!-- Agregar el token CSRF -->
                <table class="table table-striped table-borderless border-bottom pb-4">
                    <thead>
                        <form method="GET" action="#" id="search-form">
                            <tr>
                                <th class=" mt-2 pt-2">#</th>
                                <th class="mt-2 pt-2">Nombre
                                    <div class="">
                                        <input type="text" name="nombre" placeholder="area..." value="{{ request('nombre') }}" autocomplete="off" class="form-control text-xs mt-2 "/>
                                    </div>
                                </th>
                                <th class=" mt-2 pt-2">Opciones</th>
                            </tr>
                        </form>
                    </thead>
                    <tbody>
                        @php
                            $itemCount = 1;
                        @endphp
                        @foreach($areas as $us)
                            <tr>
                                <td class="">{{$us->idarea}}</td>
                                <td class="">{{$us->nombre}} </td>
                                <td>
                                    <div class="" style="display: flex;">
                                        <span class="  d-inline m-2" style="white-space: nowrap; ">
                                            <a href="{{ route('areas-show', ['id' => $us->idarea]) }}" class="btn btn-secondary text-white">
                                                <i class='bx bxs-show'></i>
                                            </a>
                                        </span>
                                        <span class=" d-inline m-2"  style="white-space: nowrap; ">
                                            <a href="{{ route('areas-edit', ['id' => $us->idarea]) }}" class="btn btn-primary text-white">
                                                <i class='bx bxs-edit-alt'></i>
                                            </a>
                                        </span>
                                        <span class="d-inline m-2" style="white-space: nowrap;">
                                            <button class="btn btn-danger text-white" onclick="confirmDelete('{{ $us->nombre }}', {{ $us->idarea }})">
                                                <i class="bx bxs-trash text-white"></i>
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @php
                            $itemCount++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                <div class="card-body px-2 pt-4 pb-1">
                    {{ $areas->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

    <script>
        let form = document.querySelector('#search-form');
        document.querySelector('input[name="nombre"]').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                form.submit();
            }
        });

        function confirmDelete(nombre, idArea) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas eliminar el área " + nombre + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('areas-destroy', ':id') }}'.replace(':id', idArea);
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
            });
        }
    </script>
</div>
@endsection
