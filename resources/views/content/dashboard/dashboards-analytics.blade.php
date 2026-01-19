@extends('layouts/contentNavbarLayout')

@section('title', 'Intranet - Inicio')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="{{ asset('assets/js/moment.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/es.global.js') }}"></script>
@endsection

@section('content')
<style>
    .card {
        transition: transform 0.4s ease;
    }

    .card:hover {
        transform: scale(1.10);
    }


</style>

<div class="container pt-2 mt-2">
    <div class="row justify-content-center">
        <div class="col-md-3 mb-4 ">
            <a href="http://asnews.asmedia.mx/" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-lg bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asnews_.png') }}" class="img-fluid mb-3" alt="Imagen ASNews">
                        <h5 class="card-title text-center text-white mb-0">As News</h5>

                        <h2 class="card-title text-center text-white mb-2">Puebla</h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4 ">
            <a href="http://asfocus.asmedia.mx/index.php?r=site%2Flogin" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-lg bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen ASNews">
                        <h2 class="card-title text-center text-white mb-2">AsFocus</h2>
                        <h5 class="card-title text-center text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="https://grupoasmedia.com/" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-lg bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Correo</h2>
                        <h5 class="card-title text-center text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-md-3 mb-4">
            <a href="http://10.49.25.67/asviewerOrigin/web/index.php?r=site%2Flogin" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asviewer.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">AS Viewer</h2>
                        <h5 class="card-title text-center text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-4">
            <a href="http://187.188.172.85:8082/glpi/index.php" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Sistema Tickets</h2>
                        <h5 class="card-title text-center text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- <div class="col-md-3 mb-4">
            <a href="http://acuerdos.asmedia.com.mx" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Acuerdos</h2>
                        <h5 class="card-title text-center text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div> -->
        <div class="col-md-3 mb-4">
            <a href="https://grupoasmedia.sharepoint.com/sites/AZP/" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">GO</h2>
                        <h5 class="card-title text-center  text-white mb-0">AS Media</h5>
                    </div>
                </div>
            </a>
        </div>

        @if (in_array('contralor', $userRole) || in_array('autorizainsumos', $userRole) || in_array('encargado', $userRole) || in_array('administrador', $userRole) || in_array('realizarequisiciones', $userRole) || in_array('recursos_materiales', $userRole))
            <div class="col-md-3 mb-4">
                <a href="{{ route('requisiciones-index') }}" class="text-decoration-none" >
                    <div class="card h-100 shadow-sm bg-dark">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                                <h2 class="card-title text-center text-white mb-2">Requisiciones</h2>
                                <h5 class="card-title text-center text-white mb-0">As media</h5>
                        </div>
                    </div>
                </a>
            </div>
        @endif

       @if(in_array($usuarioLogueadoId, [80, 316, 313, 64, 91, 340, 211, 346, 338, 334, 228, 373]))
        <div class="col-md-3 mb-4">
            <a href="http://187.188.172.85:8086/vacaciones" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Calendario</h2>
                        <h5 class="card-title text-center text-white mb-0">Vacaciones</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif

         @if(in_array($usuarioLogueadoId, [80, 316]))
        <div class="col-md-3 mb-4">
            <a href="http://10.49.25.249/login" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Encuestas</h2>
                        <h5 class="card-title text-center text-white mb-0">Grupo Asmedia</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif
        <div class="col-md-3 mb-4">
            <a href="http://buzon.asmedia.com.mx:8087/comentarios" class="text-decoration-none" target="_blank">
                <div class="card h-100 shadow-sm bg-dark">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <img src="{{ asset('assets/img/asmedia.png') }}" class="img-fluid mb-3" alt="Imagen Correo">
                        <h2 class="card-title text-center text-white mb-2">Buzon</h2>
                        <h5 class="card-title text-center  text-white mb-0">Quejas y Sugerencias</h5>
                    </div>
                </div>
            </a>
        </div>



</div>
@endsection
