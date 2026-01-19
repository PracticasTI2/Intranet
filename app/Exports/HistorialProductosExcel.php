<?php

namespace App\Exports;

use App\Models\Guia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HistorialProductosExcel implements FromView
{
    private $productosFiltrados;
    private $total;

    public function __construct($productosFiltrados, $total)
    {
        $this->productosFiltrados = $productosFiltrados;
        $this->total = $total;
    }

    public function view(): View
    {
        // Convertir el array de arrays en un array de objetos
        $objectsProductos = array_map(function ($item) {
            return (object) $item;
        }, $this->productosFiltrados);

        $total = (object) $this->total;

        //dd($objectsProductos[0]);

        return view('exports.historialexcel', compact(
            'objectsProductos',
            'total',
        ));
    }
}
