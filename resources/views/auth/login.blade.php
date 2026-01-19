@extends('layouts/blankLayout')

@section('title', 'Inicio - Sesion')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Register -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->

         <div class="app-brand justify-content-center">
           <img src="{{asset('assets/img/logos/logo_grupo_asmedia_plasta_negra.png')}}" alt="">

          </div>
          <!-- /Logo -->
          <div class="app-brand justify-content-center">
         <h4>INTRANET</h4>
          </div>

        <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="email" name="username" placeholder="" autofocus required>
        @error('username')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
            <label class="form-label" for="password">Contraseña</label>
        </div>
        <div class="input-group input-group-merge">
            <input type="password" id="password" class="form-control" name="password" aria-describedby="password" required />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
        @error('password')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    @if ($errors->any())
        <div class="mb-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit">Entrar</button>
    </div>
    <div class="mb-3">
        <a href="http://10.49.25.19/correo" class="text-secondary d-grid w-100">Registrarse</a>
    </div>
</form>


        </div>
      </div>
    </div>
    <!-- /Register -->
  </div>
</div>

</div>
@endsection
