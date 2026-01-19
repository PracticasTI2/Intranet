@extends('layouts/contentNavbarLayout')

@section('title', 'Listar Usuarios')
@section('scripts')

@endsection

@section('content')


<!-- jQuery (asegúrate de tener la última versión) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Scripts de Bootstrap (JS) -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
    </div>


  <div class="col-md-12">
    <div class="card">
      <!-- Notifications -->
      <div class="d-flex justify-content-end m-2">
        <span class="">
            <a href="{{route('usuarios-create')}}" class="btn btn-primary">Agregar Usuario</a>
        </span>
      </div>

      <div class="table-responsive ">
          @csrf <!-- Agregar el token CSRF -->
        <table class="table table-striped table-borderless border-bottom pb-4">
          <thead>
            <form method="GET" action="{{ route('usuarios-index') }}" id="search-form" >
              <tr >
                <th class=" mt-2 pt-2">#</th>
                <th class="mt-2 pt-2">Nombre
                  <div class="">

                    <input type="text" name="name" placeholder="usuario..." value="{{ request('name') }}" autocomplete="off" class="form-control text-xs mt-2 "/>
                  </div>
                </th>
                 <th class="mt-2 pt-2">Fecha Nacimiento
                  <div class="">

                    <input type="date" name="fechan" placeholder="fechan..." value="{{ request('fechan') }}" autocomplete="off" class="form-control text-xs mt-2 "/>
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

              @foreach($users as $us)
                <tr>

                  <td class="">{{$us->id}}</td>

                  <td class="">{{$us->name}} </td>
                 <td class="">{{$us->datoUsuario?->nacimiento?->format('d/m/Y') ?? 'N/A'}}</td>

                  <td>
                    <div class="" style="display: flex;">
                      <span class="  d-inline m-2" style="white-space: nowrap; "><a href="{{ route('usuarios-show', ['id' => $us->id]) }}" class="btn btn-secondary text-white"><i class='bx bxs-show' ></i></a></span>
                      <span class=" d-inline m-2"  style="white-space: nowrap; "><a  href="{{ route('usuarios-edit', ['id' => $us->id]) }}" class="btn btn-primary text-white"><i class='bx bxs-edit-alt' ></i></a></span>

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
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
      </div>

      <!-- /Notifications -->
    </div>
  </div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
      let form = document.querySelector('#search-form');

        document.querySelector('input[name="name"]').addEventListener('keypress', function (e) {
          if (e.key === 'Enter') {
              form.submit();
            }
          });

          document.querySelector('input[name="fechan"]').addEventListener('keypress', function (e) {
          if (e.key === 'Enter') {
              form.submit();
            }
          });

    </script>


</div>
@endsection
