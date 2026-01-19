@extends('layouts/contentNavbarLayout')

@section('title', 'Crear Rol')

@section('page-script')
<script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
<!-- Incluye SweetAlert -->

@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<h4 class="fw-bold py-3 mb-2">
    <span class="text-muted fw-light">Crear Rol </span>
</h4>

<div class="row">
    @if (session('success'))
        <script>
            // Asegúrate de que SweetAlert esté disponible
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                });
            } else {
                console.error('SweetAlert no está definido');
            }
        </script>
    @endif

    @if ($errors->any())
        <script>
            // Asegúrate de que SweetAlert esté disponible
            if (typeof Swal !== 'undefined') {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '{{ $error }}\n';
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Error en la validación',
                    text: errorMessages,
                });
            } else {
                console.error('SweetAlert no está definido');
            }
        </script>
    @endif
</div>

    <div class="col-md-12">
        <div class="card mb-2">
            <hr class="my-0">
            <div class="card-body">
                <form action="{{ route('roles-store') }}" method="POST" enctype="multipart/form-data">
                    @method('POST')
                    @csrf

                    <div class="row mt-4">
                        <div class="col-md-7">
                            <input type="hidden" name="guard" value="web">
                        </div>

                        <div class="col-md-6">
                            <div class="form-group m-2">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mx-2 mt-4">
                            <label for="permission"> <strong> Permisos</strong> </label>
                            <select class="form-control" name="permission[]" multiple size="10">
                                @foreach($permission as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atrás</button>
                            <input class="btn btn-success" type="submit" value="Guardar">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
