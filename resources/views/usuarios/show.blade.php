@extends('layouts/contentNavbarLayout')

@section('title', 'Listar Usuarios')
@section('scripts')
    <script src="{{ asset('js/sweet-alert.js') }}"></script>
@endsection

@section('content')
<div class="row">

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted ">Usuarios</span>

</h4>


<div class="row">
 @if (session('success'))
     <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
        <span class="alert-text"> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


  <div class="col-md-12">

    <div class="card">

      <div class="table-responsive m-2">

          <table class="table table-striped table-borderless border-bottom">
            <thead>

            </thead>
            <tbody>

            <tr><td>Usuario {{$user->name}} </td></tr>
            <tr> <td>Nickname   {{$user->email}}</td> </tr>
            <tr> <td>Empresa   {{$user->empresa}}</td> </tr>
            <tr> <td>Puesto   {{$user->puesto}}</td> </tr>


            </tbody>
          </table>

      </div>

        <div class="form-group m-2">
            <label for="roles">Roles</label>
            @foreach($userRole as $role)
                <p>{{ $role }}</p> <!-- No es necesario acceder a la propiedad 'name' -->
            @endforeach
        </div>



      <!-- /Notifications -->
    </div>
  </div>

<script>


        </script>

</div>
@endsection
