@extends('layouts/contentNavbarLayout')

@section('title', 'Ver Permiso')
@section('scripts')
    <script src="{{ asset('js/sweet-alert.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted ">Permiso : {{$permiso->name}} </span>
        </h4>
    </div>

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
                            <td class="pt-4" ><h6><strong>Roles </strong> </h6></td>
                                @foreach($rolePermissions as $rp)
                                    <tr> <td>   {{$rp->name}}</td> </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
