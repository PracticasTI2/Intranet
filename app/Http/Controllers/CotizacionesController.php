<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use App\Models\Requisicion;
use App\Models\ProductosReq;
use App\Models\Empresa;
use App\Models\Empresas;

use Illuminate\Support\Facades\Auth;
use App\Models\DatoUsuario;
use Illuminate\Support\Facades\DB;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Exports\HistorialProductosExcel;
use App\Models\Unidad;
use App\Models\Usuario;
use App\Models\TipoInsumo;
use App\Models\Area;
use App\Models\Cotizacion;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicionCreada;
use App\Mail\RequisicionAutorizada;
use App\Mail\RequisicionPreautorizada;
use App\Mail\RequisicionCotizada;
use App\Mail\CotizacionAutorizada;
use App\Mail\CotizacionesRechazadas;





class CotizacionesController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $query = Requisicion::with('cotizaciones')
            ->where('idtipo_insumo', 2)
            ->where('estado', 9)
            ->whereDoesntHave('cotizaciones'); // <-- Filtra requisiciones sin cotizaciones

        if ($request->filled('folio')) {
            $query->where('folio', $request->folio); // <-- Corrección de $request->inicio a $request->folio
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }

        if ($request->filled('estatus')) {
            $estatus = strtolower($request->estatus);

            switch ($estatus) {
                case 'pendiente':
                    $query->where('estado', 1);
                    break;
                case 'autorizado':
                    $query->where('estado', 2);
                    break;
                case 'rechazado':
                    $query->where('estado', 3);
                    break;
                case 'preautorizado':
                    $query->where('estado', 4);
                    break;
                case 'entregado':
                    $query->where('estado', 5);
                    break;
                case 'capturado':
                    $query->where('estado', 6);
                    break;
                case 'cotizado':
                    $query->where('estado', 7);
                    break;
                case 'autorizado no cotizar':
                    $query->where('estado', 8);
                    break;
                case 'autorizado necesita cotización':
                    $query->where('estado', 9);
                    break;
                case 'liberado':
                    $query->where('estado', 10);
                    break;
                default:
                    // Si no coincide con ninguna opción, buscamos por el estado tal como está
                    $query->where('estado', $request->estatus);
                    break;
            }
        }


        $requis = $query->paginate(12);

        return view('cotizaciones.index', compact('requis'));
    }


    public function cotisporautorizar(Request $request)
    {
        $query = Requisicion::with('cotizaciones')
            ->where('idtipo_insumo', 2)
            ->where('estado', 7)
            ->whereHas('cotizaciones') // Asegura que tenga cotizaciones
            ->whereDoesntHave('cotizaciones', function ($q) {
                $q->where('estatus', 1); // Excluye si alguna cotización tiene estatus 1
            });

        if ($request->filled('folio')) {
            $query->where('folio', $request->folio);
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }

        if ($request->filled('estatus')) {
            $estatus = strtolower($request->estatus);

            switch ($estatus) {
                case 'pendiente':
                    $query->where('estado', 1);
                    break;
                case 'autorizado':
                    $query->where('estado', 2);
                    break;
                case 'rechazado':
                    $query->where('estado', 3);
                    break;
                case 'preautorizado':
                    $query->where('estado', 4);
                    break;
                case 'entregado':
                    $query->where('estado', 5);
                    break;
                case 'capturado':
                    $query->where('estado', 6);
                    break;
                case 'cotizado':
                    $query->where('estado', 7);
                    break;
                case 'autorizado no cotizar':
                    $query->where('estado', 8);
                    break;
                case 'autorizado necesita cotización':
                    $query->where('estado', 9);
                    break;
                case 'liberado':
                    $query->where('estado', 10);
                    break;
                default:
                    // Si no coincide con ninguna opción, buscamos por el estado tal como está
                    $query->where('estado', $request->estatus);
                    break;
            }
        }

        $requis = $query->paginate(12);

        return view('cotizaciones.cotisporautorizar', compact('requis'));
    }

    public function cotisliberadas(Request $request)
    {
        $query = Requisicion::with('cotizaciones')
            ->where('idtipo_insumo', 2)
            ->where('estado', 10)
            ->whereHas('cotizaciones', function ($q) {
                $q->where('estatus', 1); // Solo considera cotizaciones con estatus = 1
            });

             $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;
        $userRole = $usuarioLogueado->roles->pluck('name')->all();


        if ($request->filled('folio')) {
            $query->where('folio', $request->folio);
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }

        if ($request->filled('estatus')) {
            $estatus = strtolower($request->estatus);

            switch ($estatus) {
                case 'pendiente':
                    $query->where('estado', 1);
                    break;
                case 'autorizado':
                    $query->where('estado', 2);
                    break;
                case 'rechazado':
                    $query->where('estado', 3);
                    break;
                case 'preautorizado':
                    $query->where('estado', 4);
                    break;
                case 'entregado':
                    $query->where('estado', 5);
                    break;
                case 'capturado':
                    $query->where('estado', 6);
                    break;
                case 'cotizado':
                    $query->where('estado', 7);
                    break;
                case 'autorizado no cotizar':
                    $query->where('estado', 8);
                    break;
                case 'autorizado necesita cotización':
                    $query->where('estado', 9);
                    break;
                case 'liberado':
                    $query->where('estado', 10);
                    break;
                default:
                    // Si no coincide con ninguna opción, buscamos por el estado tal como está
                    $query->where('estado', $request->estatus);
                    break;
            }
        }

        $requis = $query->paginate(12);

        return view('cotizaciones.cotisliberadas', compact('requis','userRole'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function cotizar($id)
    {
        $requisiciones = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')
            ->join('user', 'user.id', 'requisicion.id_solicitante')
            ->where('requisicion.id', $id)
            ->first();

        $productos = ProductosReq::where('folio', $requisiciones->folio)->where('empresa', $requisiciones->empresa)->get();
        $unidades = Unidad::all();

        // Obtener las cotizaciones asociadas a la requisición
        $cotizaciones = Cotizacion::where('requisicion_id', $requisiciones->id)->get();

        if (is_null($requisiciones)) {
            abort(404, 'Requisición no encontrada.');
        }

        $id_requi = $id;
        //dd($requisiciones);

        return view('cotizaciones.cotizar', compact('requisiciones', 'productos', 'cotizaciones', 'id_requi'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function completas($id)
    {
        $requisiciones = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')
            ->join('user', 'user.id', 'requisicion.id_solicitante')
            ->leftJoin('cotizaciones', 'cotizaciones.requisicion_id', 'requisicion.id')
            ->where('requisicion.id', $id)
            ->where('cotizaciones.estatus', 1)
            ->first();

        $productos = ProductosReq::where('folio', $requisiciones->folio)->where('empresa', $requisiciones->empresa)->get();
        $unidades = Unidad::all();

        // Obtener las cotizaciones asociadas a la requisición
        $cotizaciones = Cotizacion::where('requisicion_id', $id)->get();

        if (is_null($requisiciones)) {
            abort(404, 'Requisición no encontrada.');
        }
        $id_requi = $id;
        //dd($requisiciones);

        return view('cotizaciones.completas', compact('requisiciones', 'productos', 'cotizaciones', 'id_requi'));
    }

    public function update(Request $request, $id)
    {

        if ($errors = $request->validate([

            'nueva_cotizacion.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:2048',
            'nuevas_cotizaciones.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:2048',
        ]))

            DB::beginTransaction();

        try {
            $requisicion = Requisicion::findOrFail($id);

            // Reemplazar cotizaciones existentes
            if ($request->hasFile('nueva_cotizacion')) {
                foreach ($request->file('nueva_cotizacion') as $id => $file) {
                    $cotizacion = Cotizacion::find($id);
                    if ($cotizacion) {
                        Storage::disk('ftp')->delete($cotizacion->archivo);
                        $filename = $file->getClientOriginalName();
                        $filePath = $file->storeAs('/', $filename, 'ftp');
                        $cotizacion->update(['archivo' => $filePath]);
                    }
                }
            }

            // Subir nuevas cotizaciones
            if ($request->hasFile('nuevas_cotizaciones')) {
                foreach ($request->file('nuevas_cotizaciones') as $file) {
                    $filename = $file->getClientOriginalName();
                    $filePath = $file->storeAs('/', $filename, 'ftp');
                    Cotizacion::create([
                        'archivo' => $filePath,
                        'requisicion_id' => $requisicion->id,
                    ]);
                }
                $requisicion->estado = 7;
                $requisicion->save();



                     // Obtener el usuario con el rol 'rm'
                    $userRM = User::whereHas('roles', function ($query) {
                        $query->where('name', 'contralor');
                    })->first();

                    if ($userRM) {
                        $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
                        // Enviar el correo al usuario con el rol 'autorizainsumos'
                        Mail::to($userRM->correo)->send(new RequisicionCotizada($requisicion, $datos));
                    }
            }

            DB::commit();
            return redirect()->route('cotizaciones-index', $id)
                ->with('success', 'Cotizacion actualizada exitosamente');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            return redirect()->route('cotizaciones-index', $id)
                ->with('error', 'Hubo un error al actualizar la cotizacion');
        }
    }

    public function cotizacionautoriza($id)
    {

        $requisiciones = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')
            ->join('user', 'user.id', 'requisicion.id_solicitante')
            ->where('requisicion.id', $id)
            ->first();

        $productos = ProductosReq::where('folio', $requisiciones->folio)->where('empresa', $requisiciones->empresa)->get();
        $unidades = Unidad::all();

        // Obtener las cotizaciones asociadas a la requisición
        $cotizaciones = Cotizacion::where('requisicion_id', $id)->get();

        if (is_null($requisiciones)) {
            abort(404, 'Requisición no encontrada.');
        }

        $id_requi = $id;
        //dd($requisiciones);

        return view('cotizaciones.cotizacionautoriza', compact('requisiciones', 'productos', 'cotizaciones', 'id_requi'));
    }

   public function autorizarCotizacion(Request $request)
{
    $request->validate([
        'cotizacion_id' => 'required|exists:cotizaciones,id'
    ]);

    try {
        DB::beginTransaction();

        // Obtener la cotización y actualizar su estatus
        $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);
        $cotizacion->update(['estatus' => 1]);

        // Obtener la requisición y actualizar su estado
        $requisicion = Requisicion::whereKey($cotizacion->requisicion_id)->firstOrFail();
        $requisicion->update(['estado' => 10]);

        // Obtener usuarios de compras con los roles indicados y con correo válido
        $usersCompras = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['recursos_materiales', 'subir-factura']);
        })->whereNotNull('correo')->get();

         if ($usersCompras) {
         $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
        // Enviar correos
        foreach ($usersCompras as $user) {
            Mail::to($user->correo)->send(new CotizacionAutorizada($requisicion, $datos));

        }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Cotización autorizada correctamente.',
            'redirect_url' => route('cotizaciones-cotisporautorizar')
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al autorizar la cotización.',
            'error' => $e->getMessage()
        ], 500);
    }
}




public function rechazarCotizaciones(Request $request)
{
    $request->validate([
        'requisicion_id' => 'required|exists:requisicion,id'
    ]);

    try {
        DB::transaction(function () use ($request) {
            // Eliminar cotizaciones asociadas
            Cotizacion::where('requisicion_id', $request->requisicion_id)->delete();

            // Actualizar estado de la requisición
            Requisicion::where('id', $request->requisicion_id)->update(['estado' => 9]);

              $requisicion = Requisicion::whereKey($request->requisicion_id)->firstOrFail();
                // Obtener el usuario con el rol 'rm'
                $userRM = User::whereHas('roles', function ($query) {
                        $query->where('name', 'recursos_materiales');
                    })->first();

                    if ($userRM) {
                        $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $request->requisicion_id)->get();
                        // Enviar el correo al usuario con el rol 'autorizainsumos'
                        Mail::to($userRM->correo)->send(new CotizacionesRechazadas($requisicion, $datos));
                    }
        });

        return response()->json([
            'success' => true,
            'message' => 'Las cotizaciones han sido rechazadas correctamente.',
            'redirect_url' => route('cotizaciones-cotisporautorizar') // Redirección
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar cotizaciones: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


  public function guardarFactura(Request $request, $requisicion_id)
{
    $request->validate([
        'factura' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'xml_factura' => 'required|file|mimes:xml|max:2048'
    ]);

    try {
        $requisicion = Requisicion::findOrFail($requisicion_id);

        if ($request->hasFile('factura') && $request->hasFile('xml_factura')) {
            // Subir archivo de factura
            $factura = $request->file('factura');
            $facturaFilename = time() . '_' . $factura->getClientOriginalName();
            $facturaPath = $factura->storeAs('/', $facturaFilename, 'ftp');

            // Subir archivo XML de la factura
            $xmlFactura = $request->file('xml_factura');
            $xmlFacturaFilename = time() . '_' . $xmlFactura->getClientOriginalName();
            $xmlFacturaPath = $xmlFactura->storeAs('/', $xmlFacturaFilename, 'ftp');

            // Guardar las rutas de los archivos en la base de datos
            $requisicion->factura = $facturaPath;
            $requisicion->xml_factura = $xmlFacturaPath;
            $requisicion->save();

            return response()->json([
                'success' => true,
                'message' => 'Factura y XML subidos correctamente.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se seleccionaron archivos para subir.'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Hubo un error al subir la factura y el XML: ' . $e->getMessage()
        ]);
    }
}


  public function entregado(Request $request)
    {
        $request->validate([
            'id_e' => 'required|exists:requisicion,id',
            'estado_entregado' => 'required|string',
        ]);
        $requisicion = Requisicion::find($request->id_e);
        $requisicion->estado = $request->estado_entregado;
        $requisicion->save();
        return redirect()->route('cotizaciones-cotisliberadas')->with('success', 'Requisición actualizada con éxito.');
    }


    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();

        return redirect()->route('tableros-index')->with('success', 'Nota eliminada correctamente.');
    }
}
