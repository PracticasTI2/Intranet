@extends('layouts/contentNavbarLayout')

@section('title', 'Editar Registro')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-2">
  <span class="text-muted fw-light">Editar Registro</span>
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
            <form action="{{ route('tableros-update', $nota->idnota) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="titulo">Titulo</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $nota->titulo) }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{ old('descripcion', $nota->descripcion) }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="tiempo">Tiempo</label>
                            <input type="number" name="tiempo" id="tiempo" class="form-control" value="{{  $nota->tiempo }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="idtipo_tablero">Tipo de Tablero</label>
                            <select name="idtipo_tablero" id="idtipo_tablero" class="form-control">
                                <option value="" selected disabled>Selecciona una opcion</option>
                                @foreach($tipoTablero as $tipo)
                                    <option value="{{ $tipo->idtipo_tablero }}" {{ old('idtipo_tablero', $nota->idtipo_tablero) == $tipo->idtipo_tablero ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                   <div class="col-md-4">
    <div class="form-group m-2">
        <label for="tipo">Tipo</label>
        <select name="tipo" id="tipo" class="form-control">
            <option value="" selected disabled>Selecciona una opción</option>
            <option value="imagen" {{ old('tipo', $nota->tipo) == 'imagen' ? 'selected' : '' }}>Imagen</option>
            <option value="video" {{ old('tipo', $nota->tipo) == 'video' ? 'selected' : '' }}>Video</option>
            <option value="url" {{ old('tipo', $nota->tipo) == 'url' ? 'selected' : '' }}>URL</option>
        </select>
    </div>
</div>
                @php
                    $fechaPublicacion = \Carbon\Carbon::parse($nota->publicacion)->format('Y-m-d');
                    $fechaInicio = \Carbon\Carbon::parse($nota->inicio)->format('Y-m-d');
                    $fechaTermino = \Carbon\Carbon::parse($nota->termino)->format('Y-m-d');


                @endphp

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="publicacion">Publicación</label>
                            <input type="date" name="publicacion" id="publicacion" class="form-control" value="{{ $fechaPublicacion}}">

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="inicio">Inicio</label>
                            <input type="date" name="inicio" id="inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="termino">Término</label>
                            <input type="date" name="termino" id="termino" class="form-control" value="{{ $fechaTermino}}">
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group m-2">
                            <label for="fijo">Fijo</label>
                            <select name="fijo" id="fijo" class="form-control">
                                <option value="" selected disabled>Selecciona una opcion</option>
                                <option value="no" {{ old('fijo', $nota->fijo) == 'no' ? 'selected' : '' }}>No</option>
                                <option value="si" {{ old('fijo', $nota->fijo) == 'si' ? 'selected' : '' }}>Si</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        @if ($nota->tipo == 'imagen' || $nota->tipo == 'video')
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Selecciona el archivo</label>
                            <input class="form-control border border-warning" type="file" name="archivo" id="archivo">
                        </div>
                        @else
                        <div class="mb-3">
                            <label for="archivo_url" class="form-label">Pega la URL</label>
                            <input class="form-control border border-warning" type="text" name="archivo_url" id="archivo_url" value="{{ old('archivo', $nota->archivo) }}">
                        </div>
                        @endif

                        @if ($nota->archivo)
                            <p>Archivo actual:</p>
                            @if ($nota->tipo == 'imagen')
                                <img src="{{ asset('storage/' . $nota->archivo) }}" alt="Imagen actual" class="img-fluid">
                            @elseif ($nota->tipo == 'video')
                                <video controls class="w-100">
                                    <source src="{{ asset('storage/' . $nota->archivo) }}" type="video/mp4">
                                    Tu navegador no soporta la etiqueta de video.
                                </video>
                            @elseif ($nota->tipo == 'url')
                                <a href="{{ $nota->archivo }}" target="_blank">{{ $nota->archivo }}</a>
                            @else
                                <a href="{{ asset('storage/' . $nota->archivo) }}" target="_blank">{{ $nota->archivo }}</a>
                            @endif
                        @endif
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-secondary p-2 m-2" type="button" onclick="history.back()">Atras</button>
                        <input class="btn btn-success" type="submit" value="Actualizar">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
