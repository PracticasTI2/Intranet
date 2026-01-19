<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Requisición PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 16px;
        }
        .container {
            padding: 20px;
        }
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header {
            top: 0;
            margin-bottom: 10px;
        }
        .footer {
            bottom: 0;
            font-size: 14px;
        }
        .content {
            margin-top: 100px;
            margin-bottom: 60px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 5px;
            text-align: left;
        }
        .info {
            margin-bottom: 10px;
            width: 100%;
        }
        .info th, .info td {
            padding: 6px;
            border: 1px solid black;
        }
        .firmas {
            margin-top: 50px;
            text-align: center;
            width: 100%;
        }
        .firmas div {
            display: inline-block;
            width: 50%;
            text-align: start;
        }
        .firmas div p {
            margin: 0;
            padding: 10px;
            border-top: 1px solid black;
        }
    </style>
</head>
<body>
    <!-- <div class="header">

    </div> -->

    <div class="container">
        <table class="info">
            <tr>
                <th>Empresa:</th>
                <td>{{ $requisicion->rfc }}</td>
                <th><img src="{{ asset('assets/img/logo1.jpg') }}" alt="Logo" style="width: 100px;"></th>
            </tr>


            <tr>
                <th>Fecha:</th>
                <td>{{ $requisicion->fechasol->format('d/m/Y') }}</td>
                <th>Folio No.: {{ $requisicion->folio }}</th>

            </tr>
            <tr>
                <th>Solicitante:</th>
                <td>{{ $requisicion->solicitante }}</td>

                <th>
                    @if($requisicion->estado== 1)
                        Estado: Pendiente
                    @elseif($requisicion->estado== 2)
                        Estado: Autorizado
                    @elseif($requisicion->estado== 3)
                        Estado: Rechazado
                    @elseif($requisicion->estado== 4)
                        Estado: Preautorizado
                    @elseif($requisicion->estado== 5)
                        Estado: Entregado
                     @elseif($requisicion->estado== 6)
                        Estado: Capturado
                     @elseif($requisicion->estado== 7)
                        Estado: Cotizado
                     @elseif($requisicion->estado== 8)
                        Estado: Autorizado sin Cotizar
                     @elseif($requisicion->estado== 9)
                        Estado: Autorizado Necesita Cotización
                     @elseif($requisicion->estado== 10)
                        Estado: Liberado
                    @else
                        Estado: Sin estatus
                    @endif
                </th>
            </tr>
            <tr>
                <th>Área o Dpto.:</th>
                <td>{{ $requisicion->area }}</td>

                <th>
                    Tipo
                    @if($requisicion->idtipo_insumo== 1)
                    : Consumibles
                    @else
                    : Especiales
                    @endif

                </th>
            </tr>


        </table>

        <div class="content">
            <table class="table">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>CANT.</th>
                        <th>UND.</th>

                        <th>DESCRIPCIÓN</th>
                        <th>JUSTIFICACIÓN</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $index => $producto)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $producto->cantidad }}</td>
                        <td>{{ $producto->unidad }}</td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>{{ $producto->justificacion }}</td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
              <!-- <div class="observaciones">

            </div> -->
            <!-- <div class="observaciones">
                <p>OBSERVACIONES: {{ $requisicion->respuesta }}</p>
            </div> -->
            <div class="firmas">
                <div>
                    <p>Recibe : </p>
                    <p></p>
                </div>

            </div>
        </div>
    </div>
    <!-- <div class="footer">
        <p>Eminus Sa de CV</p>
    </div> -->
</body>
</html>
