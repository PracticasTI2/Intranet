@extends('layouts/contentNavbarLayout')

@section('title', 'Ver Area')
@section('scripts')
    <script src="{{ asset('js/sweet-alert.js') }}"></script>
@endsection

@section('content')
<div class="row">

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted ">Areas</span>

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

            <tr><td>Area {{$areas->nombre}} </td></tr>

            <tr> <td>Encargado   {{$areas->name}}</td> </tr>


            </tbody>
          </table>

      </div>





      <!-- /Notifications -->
    </div>
  </div>

<script>


        </script>

</div>
@endsection
