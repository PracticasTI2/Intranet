@extends('layouts/contentNavbarLayout')

@section('title', 'Crear Usuario')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-2">
  <span class="text-muted fw-light">Crear Usuario </span>
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
        <div class="card mb-2">
        <hr class="my-0">
            <div class="card-body">
                <form action="{{ route('usuarios-store') }}" method="POST" enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                    <input type="hidden" name="guard" value="web">
                    <div class="row">
                        <label for=""><strong>Para configurar con LDAP </strong> </label>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="name">Name</label>
                                <input placeholder="Nombre Apellidos en LDAP" type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="email">Username</label>
                                <input placeholder="Usuario igual que en LDAP" type="text" name="email" id="email" class="form-control" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>


                    <div class="row mt-4">
                        <label for=""><strong>  Para configurar datos Usuario </strong></label>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="apaterno">Apellido Paterno</label>
                                <input type="text" name="apaterno" id="apaterno" class="form-control" value="{{ old('apaterno') }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="amaterno">Apellido Materno</label>
                                <input type="text" name="amaterno" id="amaterno" class="form-control" value="{{ old('amaterno') }}" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="ingreso">Fecha de ingreso</label>
                                <input type="date" name="ingreso" id="ingreso" class="form-control" value="{{ old('ingreso') }}">
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="form-group m-2">
                                <label for="nacimiento">Fecha de Nacimiento</label>
                                <input type="date" name="nacimiento" id="nacimiento" class="form-control" value="{{ old('nacimiento') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="egreso">Fecha de egreso</label>
                                <input type="date" name="egreso" id="egreso" class="form-control" value="{{ old('egreso') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="puesto">Puesto</label>
                                <select name="puesto" id="puesto" class="form-control">
                                    <option value="" selected disabled>Selecciona una opcion</option>
                                    @foreach($puesto as $p)
                                        <option value="{{ $p->idpuesto }}" {{ old('puesto') == $p->idpuesto ? 'selected' : '' }}>
                                            {{ $p->puesto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="area">Area</label>
                                <select name="area" id="area" class="form-control">
                                    <option value="" selected disabled>Selecciona una opcion</option>
                                    @foreach($areas as $ar)
                                        <option value="{{ $ar->idarea }}" {{ old('area') == $ar->idarea ? 'selected' : '' }}>
                                            {{ $ar->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="jefe">Jefe</label>
                                <select name="jefe" id="jefe" class="form-control">
                                    <option value="" selected disabled>Selecciona un usuario</option>
                                    @foreach($usuarios as $jefe)
                                        <option value="{{ $jefe->id }}" {{ old('jefe') == $jefe->id ? 'selected' : '' }}>
                                            {{ $jefe->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="correo">Correo</label>
                                <input placeholder="ejemplo@grupoasmedia.com" type="email" name="correo" id="correo" class="form-control" value="{{ old('correo') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check mt-5">
                                <label class="form-check-label " for="visible">
                                    Visible
                                </label>
                                <input class="form-check-input" type="checkbox" name="visible" id="visible" {{ old('visible') ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="col-md-4">

                        </div>
                        <div class="col-md-6 pt-2">
                            <div class="form-group m-2">
                                <label for="roles">Roles</label>
                                <select name="roles[]" id="roles"  size="8" class="form-select" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="d-flex justify-content-center mb-4">
                                <img id="selectedAvatar" src="https://mdbootstrap.com/img/Photos/Others/placeholder-avatar.jpg"
                                class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="example placeholder" />
                            </div>
                            <div class="d-flex justify-content-center">
                                <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                    <label class="form-label text-white m-1" for="foto">Subir Foto</label>
                                    <input type="file" class="form-control d-none" name="foto" id="foto" onchange="displaySelectedImage(event, 'selectedAvatar')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                            <input class="btn btn-success" type="submit" value="Guardar">
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <script>
        function displaySelectedImage(event, elementId) {
        const selectedImage = document.getElementById(elementId);
        const fileInput = event.target;

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                selectedImage.src = e.target.result;
            };

            reader.readAsDataURL(fileInput.files[0]);
        }
    }

  </script>
    </div>
@endsection
