@extends('layouts/contentNavbarLayout')

@section('title', 'Editar Rol')

@section('page-script')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Editar Rol </span>
    </h4>

    <div class="row">
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
    </div>

    <div class="col-md-12">
        <div class="card mb-4">
            <hr class="my-0">
            <div class="card-body">
                <!-- Formulario para editar rol -->
                <form action="{{ route('roles-update', $role->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <!-- Mostrar errores si existen -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <!-- Nombre del rol -->
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}">
                            </div>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permisos asociados al rol -->
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="permission">Permisos</label>
                                <select name="permission[]" class="form-control" multiple size="10">
                                    @foreach($permission as $p)
                                        <option value="{{ $p->id }}"
                                            {{ in_array($p->id, $rolePermissions) ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('permission')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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
