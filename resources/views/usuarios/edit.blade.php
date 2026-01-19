@extends('layouts/contentNavbarLayout')

@section('title', 'Editar Usuario')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Editar Usuario </span>
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
                                <form action="{{ route('usuarios-update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf

                                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="guard" value="web">
                    <input type="hidden" name="name" id="name" class="form-control" value="{{ $user->name }}">
                    <input type="hidden" name="email" id="email" class="form-control" value="{{ $user->email }}">


                       <div class="row">
                        <label for=""><strong>Para configurar con LDAP </strong> </label>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="name">Name</label>
                                <input placeholder="Nombre Apellidos en LDAP" type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="email">Username</label>
                                <input placeholder="Usuario igual que en LDAP" type="text" name="email" id="email" class="form-control" value="{{ $user->email }}"">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                         <label for=""><strong>  Para configurar datos Usuario </strong></label>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $user->nombre }}">


                            </div>
                              @error('nombre')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="apaterno">Apellido Paterno</label>
                                <input type="text" name="apaterno" id="apaterno" class="form-control" value="{{ $user->apaterno }}">
                            </div>
                              @error('apaterno')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="amaterno">Apellido Materno</label>
                                <input type="text" name="amaterno" id="amaterno" class="form-control" value="{{ $user->amaterno }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="ingreso">Fecha de ingreso</label>
                                <input type="date" name="ingreso" id="ingreso" class="form-control" value="{{ $user->ingreso }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="nacimiento">Fecha de Nacimiento</label>
                                <input type="date" name="nacimiento" id="nacimiento" class="form-control" value="{{ $user->nacimiento }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="egreso">Fecha de egreso</label>
                                <input type="date" name="egreso" id="egreso" class="form-control" value="{{ $user->egreso }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="puesto">Puesto</label>
                                <select name="puesto" id="puesto" class="form-control">
                                    @foreach($puesto as $p)
                                        <option value="{{ $p->idpuesto }}" {{ isset($userPuesto) && $userPuesto->idpuesto == $p->idpuesto ? 'selected' : '' }}>
                                            {{ $p->puesto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                       <div class="col-md-4">
                            <div class="form-group m-2">
                                <label for="area">Área</label>
                                <select name="area" id="area" class="form-control">
                                    @foreach($areas as $ar)
                                        <option value="{{ $ar->idarea }}" {{ isset($areaUser) && $areaUser->idarea == $ar->idarea ? 'selected' : '' }}>
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
                                        <option value="{{ $jefe->id }}" {{ ($userJefe && $userJefe->id == $jefe->id) ? 'selected' : '' }}>
                                            {{ $jefe->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group m-2">
                                <label for="correo">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control" value="{{ $user->correo }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-check mt-5">
                                <label class="form-check-label " for="visible">
                                    Visible
                                </label>

                                <input class="form-check-input" type="checkbox" name="visible"   id="visible"  {{ $user->visible ==1 ? 'checked' : ''}} >
                            </div>
                        </div>

                        <div class="col-md-2">

                        </div>

                        <div class="col-md-6 pt-2">
                            <div class="form-group m-2">
                                <label for="roles">Roles</label>
                                <select name="roles[]" id="roles" class="form-select" size="8" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ in_array($role->id, $userRole) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4 pt-4">
                            <div class="d-flex justify-content-center mb-4">
                                <img id="selectedAvatar" src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://mdbootstrap.com/img/Photos/Others/placeholder-avatar.jpg' }}"
                                    class="rounded-circle" style="position: center; width: 100px; height: 100px; object-fit: fill;" alt="foto" />
                            </div>
                            <div class="d-flex justify-content-center">
                                <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                    <label class="form-label text-white m-1" for="foto">Subir Foto</label>
                                    <input type="file" class="form-control d-none" name="foto" id="foto" onchange="displaySelectedImage(event, 'selectedAvatar')" />
                                </div>
                            </div>

                                @error('foto')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
