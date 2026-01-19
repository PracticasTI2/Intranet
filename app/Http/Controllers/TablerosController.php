<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use App\Models\Requisicion;
use App\Models\ProductosReq;
use App\Models\Empresa;
use App\Models\Empresas;

use App\Models\Nota;
use App\Models\TipoTablero;

use Illuminate\Support\Facades\Auth;
use App\Models\DatoUsuario;
use Illuminate\Support\Facades\DB;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TablerosController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index(Request $request)
    {
        $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;

        $query = Nota::with('tipo_tablero');


        if ($request->filled('inicio')) {
             $query->whereDate('inicio', $request->inicio);
         }
         if ($request->filled('termino')) {
             $query->whereDate('termino', $request->termino);
         }

        if ($request->filled('titulo')) {
             $query->where('titulo', 'like', '%' . $request->titulo . '%');
         }



        $tableros = $query->paginate(8);

        return view('tableros.index', compact('tableros'));
    }



    public function pantalla()
    {
        $currentMonth = Carbon::now()->month;

        // Obtener usuarios que cumplen años este mes
        // $cumpleaneros = DatoUsuario::join('puesto','puesto.idpuesto', 'dato_usuario.puesto_idpuesto')->whereMonth('nacimiento', $currentMonth)->get();
        $cumpleaneros = DatoUsuario::join('puesto', 'puesto.idpuesto', '=', 'dato_usuario.puesto_idpuesto')
            ->whereMonth('nacimiento', $currentMonth)
            ->where('visible', 1)
            ->get();

        // Obtener notas vigentes
        $notas = Nota::where('inicio', '<=', Carbon::now())
                      ->where('termino', '>=', Carbon::now())
                      ->orWhere(function($query) {
                          $query->where('fijo', true)
                                ->where('iniciofijo', '<=', Carbon::now())
                                ->where('terminofijo', '>=', Carbon::now());
                      })
                      ->get();

        return view('tableros.pantalla', compact('cumpleaneros', 'notas'));
    }

//     public function pantalla()
// {
//     $currentMonth = Carbon::now()->month;

//     // Obtener usuarios que cumplen años este mes
//     $cumpleaneros = DatoUsuario::join('puesto','puesto.idpuesto', 'dato_usuario.puesto_idpuesto')->whereMonth('nacimiento', $currentMonth)->get();

//     // Obtener notas vigentes
//     $notas = Nota::join('tipo_tablero', 'nota.idtipo_tablero', '=', 'tipo_tablero.idtipo_tablero')
//                   ->where(function($query) {
//                       $query->where('nota.inicio', '<=', Carbon::now())
//                             ->where('nota.termino', '>=', Carbon::now())
//                             ->orWhere(function($query) {
//                                 $query->where('nota.fijo', true)
//                                       ->where('nota.inicioFijo', '<=', Carbon::now())
//                                       ->where('nota.terminoFijo', '>=', Carbon::now());
//                             });
//                   })
//                   ->select('nota.*', 'tipo_tablero.porcentaje', 'nota.tiempo as tablero_tiempo')
//                   ->get();

//     return view('tableros.pantalla', compact('cumpleaneros', 'notas'));
// }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $usuarioLogueadoId = Auth::user()->id;
        $tipoTablero = TipoTablero::get();

        return view('tableros.create', compact('tipoTablero'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
{
    // Initialize the base validation rules
    $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string|max:255',
        'tiempo' => 'required|integer',
        'tipoTablero' => 'required|integer|exists:tipo_tablero,idtipo_tablero',
        'publicacion' => 'required|date',
        'inicio' => 'required|date',
        'termino' => 'required|date',
    ];
// dd($request->all());
    // Add conditional validation rule for 'archivo' based on the 'tipo'
    if ($request->input('tipo') === 'url') {
        $rules['archivo_url'] = 'required|string|max:455';
    } else {
        $rules['archivo'] = 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi';
    }

    // Validate the request with the combined rules
    $validatedData = $request->validate($rules);

    // Continue with your logic to store the data
    $nota = new Nota();
    $nota->titulo = $validatedData['titulo'];
    $nota->descripcion = $validatedData['descripcion'];
    $nota->tiempo = $validatedData['tiempo'];
    $nota->idtipo_tablero = $validatedData['tipoTablero'];
    $nota->tipo = $request->input('tipo');
    $nota->publicacion = $validatedData['publicacion'];
    $nota->inicio = $validatedData['inicio'];
    $nota->termino = $validatedData['termino'];
    $nota->fijo = $request->input('fijo');

    // Handle 'archivo' field based on the 'tipo'
    if ($nota->tipo === 'url') {
        $nota->archivo = $validatedData['archivo_url'];
    } elseif ($request->hasFile('archivo')) {
        $archivo = $request->file('archivo');
        $extension = $archivo->getClientOriginalExtension();
        $nombreArchivo = Str::slug($validatedData['titulo']) . '-' . time() . '.' . $extension;
        $archivoPath = $archivo->storeAs('archivos', $nombreArchivo, 'public');
        $nota->archivo = $archivoPath;
    }

    $nota->save();

    return redirect()->route('tableros-index')->with('success', 'Tablero agregado exitosamente');
}
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Imprimir el valor de $id para depurar



        // Realizar la consulta con el ID normalizado
        $requisiciones = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')
            ->join('user', 'user.id', 'requisicion.id_solicitante')
            ->where('requisicion.id', $id)
            ->first();



        if (is_null($requisiciones)) {
            abort(404, 'Requisición no encontrada.');
        }

        $productos = ProductosReq::where('folio', $requisiciones->folio)
            ->where('empresa', $requisiciones->empresa)
            ->get();

        return view('requisiciones.show', compact('requisiciones', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function edit($id)
{
    $nota = Nota::where('nota.idnota', $id)->first();
    $tipoTablero = TipoTablero::all();


    return view('tableros.edit', compact('nota', 'tipoTablero'));
}

public function update(Request $request, $id)
{
    $nota = Nota::findOrFail($id);

    // Reglas de validación
    $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string|max:255',
        'tiempo' => 'nullable|integer',
        'idtipo_tablero' => 'required',
        'tipo' => 'required|string|in:imagen,video,url',
        'publicacion' => 'nullable|date',
        'inicio' => 'nullable|date',
        'termino' => 'nullable|date',
        'fijo' => 'required|string|in:si,no',
    ];

    if ($request->input('tipo') === 'url') {
        $rules['archivo_url'] = 'required|string|max:455';
    } else {
        $rules['archivo'] = 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov';
    }

    $validatedData = $request->validate($rules);

    // Actualizar los datos de la nota
    $nota->update($validatedData);

    // Manejo del archivo
    if ($request->input('tipo') === 'url') {
        $nota->archivo = $validatedData['archivo_url'];
    } elseif ($request->hasFile('archivo')) {
        // Eliminar archivo anterior si existe
        if ($nota->archivo) {
            Storage::disk('public')->delete($nota->archivo);
        }

        // Guardar el nuevo archivo con el mismo formato que en store
        $archivo = $request->file('archivo');
        $extension = $archivo->getClientOriginalExtension();
        $nombreArchivo = Str::slug($validatedData['titulo']) . '-' . time() . '.' . $extension;
        $archivoPath = $archivo->storeAs('archivos', $nombreArchivo, 'public');
        $nota->archivo = $archivoPath;
    }

    $nota->save();

    return redirect()->route('tableros-index', $nota->idnota)->with('success', 'Registro actualizado exitosamente');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();

        return redirect()->route('tableros-index')->with('success', 'Nota eliminada correctamente.');
    }
}
