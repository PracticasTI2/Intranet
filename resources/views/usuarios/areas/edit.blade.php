@extends('layouts/contentNavbarLayout')

@section('title', 'Editar Area')

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Editar Area </span>
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
                        <form action="{{ route('areas-update', $areas->idarea) }}" method="POST" enctype="multipart/form-data">
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

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group m-2">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $areas->nombre }}">
                                    </div>
                                    @error('nombre')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group m-2">
                                        <label for="encargado">Encargado</label>
                                        <select name="encargado" id="encargado" class="form-control">
                                            <option value="" selected disabled>Selecciona un usuario</option>
                                            @foreach($usuarios as $encargado)
                                                <option value="{{ $encargado->id }}" {{ ($userEnc && $userEnc->id == $encargado->id) ? 'selected' : '' }}>
                                                    {{ $encargado->name }}
                                                </option>
                                            @endforeach
                                        </select>
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
        </div>


@endsection
