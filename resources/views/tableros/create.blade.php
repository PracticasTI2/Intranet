@extends('layouts/contentNavbarLayout')

@section('title', 'Crear Registro')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-2">
  <span class="text-muted fw-light">Crear Registro </span>
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
            <form action="{{ route('tableros-store') }}" method="POST" enctype="multipart/form-data">
                @csrf


                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="titulo">Titulo</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{ old('descripcion') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="tiempo">Tiempo</label>
                            <input type="number" name="tiempo" id="tiempo" class="form-control" value="{{ old('tiempo') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="tipoTablero">Tipo de Tablero</label>
                            <select name="tipoTablero" id="tipoTablero" class="form-control">
                                <option value="" selected disabled>Selecciona una opcion</option>
                                @foreach($tipoTablero as $tipo)
                                    <option value="{{ $tipo->idtipo_tablero }}" {{ old('tipoTablero') == $tipo->idtipo_tablero ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                   <div class="col-md-4">
    <div class="form-group m-2">
        <label for="tipo">Tipo</label>
        <select name="tipo" id="tipo" class="form-control" onchange="toggleInputFields()">
            <option value="" selected disabled>Selecciona una opción</option>
            <option value="imagen" {{ old('tipo') == 'imagen' ? 'selected' : '' }}>Imagen</option>
            <option value="video" {{ old('tipo') == 'video' ? 'selected' : '' }}>Video</option>
            <option value="url" {{ old('tipo') == 'url' ? 'selected' : '' }}>URL</option>
        </select>
    </div>
</div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="publicacion">Publicación</label>
                            <input type="date" name="publicacion" id="publicacion" class="form-control" value="{{ old('publicacion') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="inicio">Inicio</label>
                            <input type="date" name="inicio" id="inicio" class="form-control" value="{{ old('inicio') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="termino">Termino</label>
                            <input type="date" name="termino" id="termino" class="form-control" value="{{ old('termino') }}">
                        </div>
                    </div>

                     <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="fijo">Fijo</label>
                            <select name="fijo" id="fijo" class="form-control">
                                <option value="" selected disabled>Selecciona una opcion</option>
                                <option value="no" {{ old('fijo') == 'no' ? 'selected' : '' }}>No</option>
                                <option value="si" {{ old('fijo') == 'si' ? 'selected' : '' }}>Si</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4" id="fileInputDiv">
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Selecciona el archivo</label>
                            <input class="form-control border border-warning" type="file" name="archivo" id="archivo">
                        </div>
                    </div>

                    <div class="col-md-4" id="urlInputDiv" style="display:none;">
                        <div class="mb-3">
                            <label for="archivo_url" class="form-label">Ingresa la URL</label>
                            <input class="form-control border border-warning" type="text" name="archivo_url" id="archivo_url" value="{{ old('archivo_url') }}">
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
    function toggleInputFields() {
        const tipo = document.getElementById('tipo').value;
        const fileInputDiv = document.getElementById('fileInputDiv');
        const urlInputDiv = document.getElementById('urlInputDiv');

        if (tipo === 'url') {
            fileInputDiv.style.display = 'none';
            urlInputDiv.style.display = 'block';
        } else {
            fileInputDiv.style.display = 'block';
            urlInputDiv.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleInputFields(); // Ejecutar al cargar la página para verificar el estado inicial
    });
</script>
</div>
@endsection
