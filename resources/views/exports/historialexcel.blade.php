<table>
    <thead>
        <tr>
            <th align="center" width='120px'># Folio</th>
            <th align="center" width='120px'>Descripción</th>
            <th align="center" width='200px'>Justificación</th>
            <th align="center" width='120px'>Solicitante</th>
            <th align="center" width='120px'>Área</th>
            <th align="center" width='120px'>Estatus</th>
            <th align="center" width='120px'>Fecha Solicitud</th>
            <th align="center" width='120px'>Tipo de Insumo</th>
            <th align="center" width='120px'>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($objectsProductos as $producto)
            <tr>
                <td align="center">{{ $producto->folio }}</td>

                <td>{{ $producto->descripcion }}</td>
                <td>{{ $producto->justificacion }}</td>

                <td>{{ $producto->solicitante }}</td>

                <td>{{ $producto->area }}</td>

                <td align="center">
                    @if ($producto->estado == 1)
                        <span>Pendiente</span>
                    @elseif($producto->estado == 2)
                        <span>Autorizado</span>
                    @elseif($producto->estado == 3)
                        <span>Rechazado</span>
                    @elseif($producto->estado == 4)
                        <span>Preautorizado</span>
                    @elseif($producto->estado == 5)
                        <span>Entregado</span>
                    @elseif($producto->estado == 6)
                        <span>Capturado</span>
                    @endif
                </td>
                <td align="center">{{ \Carbon\Carbon::parse($producto->fechasol)->format('d/m/Y') }}</td>
                <td align="center">
                    @if ($producto->idtipo_insumo == 1)
                        <span>Consumibles</span>
                    @else
                        <span>Especiales</span>
                    @endif
                </td>
                <td align="center">{{ $producto->cantidad }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total </td>
            <td align="center"> {{ $total->cantidad_total }} </td>
        </tr>
    </tbody>
</table>
