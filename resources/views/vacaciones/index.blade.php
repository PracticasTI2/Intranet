@extends('layouts/contentNavbarLayout')

@section('title', 'Calendario - Vacaciones')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/es.global.js') }}"></script>
@endsection

@section('content')
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>


    <div class="row justify-content-center"> {{-- Content para el calendario --}}

        <div class="col-md-10 col-lg-10 col-xl-10  mb-4">

            <div class="row"> {{-- Estatus --}}
                <div class="col">
                    <h4 class=" text-center">Estatus : </h4>
                </div>

                <div class="col">
                    <p class="text-white text-center" style="background-color: #808080;">Pendiente</p>
                </div>
                <div class="col">
                    <p class="text-white  text-center" style="background-color: #276621;">Autorizado</p>
                </div>
                <div class="col">
                    <p class="text-white text-center" style="background-color: #FF0000;">Rechazado</p>
                </div>
            </div>

            <div class="card justify-content-center"> {{-- Calendario  --}}

                <div class="card-body ">
                    <div id='calendar'></div>
                </div>

                <h3> hola {{$user}} </h3>

                {{-- <input type="" value="{{ $sam }}" id="useractual" name="useractual"> --}}

                {{-- Modal de  --}}
                <div class="modal fade show" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    aria-labelledby="myModal" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc">

                        <div class="modal-content">
                            <div class="modal-header bg-secondary  ">
                                <h5 class="modal-title text-white" id="titulo"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                        </div>

                        <form id="formulario" action=""> {{-- Formulario --}}
                            <div class="modal-body">

                                <input type="hidden" id="id" name="id">

                                {{-- <div class="form-floating mb-3">

                                    @if ($rol === 2)
                                        <input type="text" class="form-control" id="nombre_user" name="nombre_user"
                                            value="{{ $ldapName }}" readonly>
                                        <label for="nombre_user">Usuario</label>
                                    @else
                                        <input type="text" class="form-control" id="nombre_user" name="nombre_user"
                                            value="" required>
                                        <label for="nombre_user">Evento</label>
                                    @endif

                                </div> --}}

                                <div class="form-floating mb-3"> {{-- Fecha inicio --}}
                                    <input type="date" class="form-control" id="fechain" name="fechain">
                                    <label for="fechain">Fecha de Inicio</label>
                                </div>

                                <div class="form-floating mb-3"> {{-- Fecha fin --}}
                                    <input type="date" class="form-control" id="fechafin" name="fechafin">
                                    <label for="fechafin">Fecha final</label>
                                </div>

                            </div>

                            <div class="modal-footer"> {{-- Footer --}}

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <a class="btn btn-danger text-white" id="btnEliminar" name="btnEliminar">Eliminar</a>
                                <button class="btn btn-primary" id="btnAccion" type="submit">Registrar</button>

                                {{-- @if ($rol === 2)
                                    <div class="d-none">
                                    @else
                                        <div>
                                @endif --}}

                                <a class="btn btn-success text-white" id="btnAutorizar" name="btnAutorizar">Autorizar</a>

                            </div>

                            {{-- TODO: revisar estaba en hidden --}}
                            {{-- <input type="" name="iduserev" id="iduserev" value="{{ $iduser }}"> --}}


                        </form>

                    </div>

                </div>

            </div>

            {{-- <input type="hidden" name="idrol" id="idrol" value="{{ $rol }}">
            <input type="hidden" name="iduser" id="iduser" value="{{ $iduser }}"> --}}

        </div>
    </div>

    <script>
        var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        let frm = document.getElementById('formulario');
        var eliminar = document.getElementById('btnEliminar');
        var autorizar = document.getElementById('btnAutorizar');

        // var APP_URL = {!! json_encode(url('/')) !!} ;
        var APP_URL = {!! json_encode(url('/')) !!};

    </script>





@endsection
