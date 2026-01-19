<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use App\Models\Requisicion;
use App\Models\ProductosReq;
use App\Models\Empresa;
use App\Models\Empresas;
use App\Exports\HistorialProductosExcel;
use App\Models\Unidad;
use Illuminate\Support\Facades\Auth;
use App\Models\DatoUsuario;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\TipoInsumo;
use App\Models\Area;
use App\Models\Cotizacion;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicionCreada;
use App\Mail\RequisicionAutorizada;
use App\Mail\RequisicionPreautorizada;
use App\Mail\RequisicionAutconCot;

class RequisicionesController extends Controller
{
    function __construct()
    {

        $this->middleware(['role:contralor|autorizainsumos|jefe|encargado'])->only('autorizar');

        $this->middleware(['role:administrador|contralor|autorizainsumos|recursos_materiales'])->only(
            'destroy',
            'historico',
            //'historial',
            'historialproductos',
        );

        $this->middleware(['role:recursos_materiales'])->only('entregado');

        $this->middleware(['role:realizarequisiciones|administrador|autorizainsumos|contralor|jefe|encargado|recursos_materiales'])->only(
            'index',
            'show',
            'create',
            'store',
            'edit',
            'update',
            'pendiente',
            'historial'
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   public function index(Request $request)
    {
        $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;
        $userRole = $usuarioLogueado->roles->pluck('name')->all();
        $usuariosDeJefe = DB::table('dato_usuario')->where('idjefe', $usuarioLogueadoId)->pluck('user_id')->toArray();
        $estado_array = [];

        if (in_array('administrador', $userRole)) {
            $estado_array = [1, 2, 3, 4, 5, 6];
        }
        if (in_array('contralor', $userRole)) {
            $estado_array[] = 4;
        }
        if (in_array('encargado', $userRole)) {
            $estado_array[] = 1;
        }
        if (in_array('autorizainsumos', $userRole)) {
            $estado_array[] = 4;
        }
        if (in_array('recursos_materiales', $userRole)) {
            $estado_array[] = 2;
        }

        $estado_array = array_unique($estado_array);

        $query = Requisicion::select('requisicion.*')
            ->with('empresas')
            ->leftJoin('dato_usuario', 'requisicion.id_solicitante', '=', 'dato_usuario.user_id')
            ->leftJoin('area', 'dato_usuario.area_idarea', '=', 'area.idarea')
            ->where(function ($query) use ($userRole, $usuarioLogueadoId, $estado_array) {
                $query->where(function ($query) use ($userRole, $usuarioLogueadoId, $estado_array) {
                    if (in_array('realizarequisiciones', $userRole)) {
                        $query->where('id_solicitante', $usuarioLogueadoId);
                    } else {
                        $query->whereIn('estado', $estado_array);
                    }
                });

                if (in_array('contralor', $userRole)) {
                    $query->where('idtipo_insumo', 2);
                }
                if (in_array('autorizainsumos', $userRole)) {
                    $query->where('idtipo_insumo', 1);
                }
                if (in_array('encargado', $userRole)) {
                    $query->where('area.id_encargado', $usuarioLogueadoId);
                }
                $query->orWhere('id_solicitante', $usuarioLogueadoId);
            });

        if ($request->filled('empresa')) {
            $query->whereHas('empresas', function ($q) use ($request) {
                $q->where('rfc', 'like', '%' . $request->empresa . '%');
            });
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
                case 'autorizado necesita cotizacion':
                    $query->where('estado', 9);
                    break;
                case 'liberado':
                    $query->where('estado', 10);
                    break;
                default:
                    $query->where('estado', $request->estatus);
                    break;
            }
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fechasol', $request->fecha);
        }

        if ($request->filled('folio')) {
            $query->where('folio', $request->folio);
        }

        $requisiciones_all = clone $query; // Clonar la consulta original
        $all_r = $requisiciones_all->get();

        // Verificar si existen requisiciones del usuario logueado
        $existeRequisicion = $all_r->contains('id_solicitante', $usuarioLogueadoId);

        if (!$existeRequisicion) {
            $query->whereNotIn('estado', [7, 9, 10]);
        }

        $query->orderBy('folio', 'desc');

        $requisiciones = $query->paginate(10);
        $requisiciones->appends(request()->except('page'));

        return view('requisiciones.index', compact('requisiciones', 'userRole', 'usuariosDeJefe', 'usuarioLogueadoId'));
    }


    public function historico(Request $request)
    {
        $query = Requisicion::with('empresas');

        // Filtros adicionales
        if ($request->filled('empresa')) {
            $query->whereHas('empresas', function ($q) use ($request) {
                $q->where('rfc', 'like', '%' . $request->empresa . '%');
            });
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }

        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
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
                case 'autorizado necesita cotizacion':
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

        if ($request->filled('tipo')) {
            $tipo = strtolower($request->tipo);

            switch ($tipo) {
                case 'consumibles':
                    $query->where('idtipo_insumo', 1);
                    break;
                case 'especiales':
                    $query->where('idtipo_insumo', 2);
                    break;
                default:
                    // Si no coincide con ninguna opción, buscamos por el estado tal como está
                    $query->where('idtipo_insumo', $request->tipo);
                    break;
            }
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fechasol', $request->fecha);
        }

        if ($request->filled('folio')) {
            $query->where('folio', $request->folio);
        }

        $query->orderBy('fechasol', 'desc');

        $requisiciones = $query->paginate(10);

        $requisiciones->appends(request()->except('page'));

        return view('requisiciones.historico', compact('requisiciones'));
    }


    public function historial(Request $request)
    {
        $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;
        $userRole = $usuarioLogueado->roles->pluck('name')->all();
        $usuariosDeJefe = DB::table('dato_usuario')->where('idjefe', $usuarioLogueadoId)->pluck('user_id')->toArray();

        $query = Requisicion::select('requisicion.*')->with('empresas');

        $query->leftJoin('dato_usuario', 'requisicion.id_solicitante', '=', 'dato_usuario.user_id')
            ->leftJoin('area', 'dato_usuario.area_idarea', '=', 'area.idarea')
            ->where(function ($query) use ($userRole, $usuarioLogueadoId, $usuariosDeJefe) {
                if (in_array('administrador', $userRole)) {
                    $query->where(function ($query) use ($usuarioLogueadoId) {
                        $query->whereIn('estado', [1, 2, 3, 4])
                            ->orWhere('id_solicitante', $usuarioLogueadoId);
                    });
                } elseif (in_array('contralor', $userRole)) {
                    $query->whereIn('estado', [4, 2])
                        ->where('idtipo_insumo', 2)
                        ->orWhere('id_solicitante', $usuarioLogueadoId);
                } elseif (in_array('encargado', $userRole)) {
                    $query->whereIn('requisicion.estado', [1, 2, 3, 4, 5, 6])
                        ->whereIn('requisicion.idtipo_insumo', [1, 2])
                        ->where('area.id_encargado', $usuarioLogueadoId)
                        ->orWhere('id_solicitante', $usuarioLogueadoId);
                } elseif (in_array('autorizainsumos', $userRole)) {
                    $query->whereIn('requisicion.estado', [4, 2])
                        ->where('requisicion.idtipo_insumo', 1)
                        ->orWhere('id_solicitante', $usuarioLogueadoId);
                } elseif (in_array('realizarequisiciones', $userRole)) {
                    $query->where('id_solicitante', $usuarioLogueadoId);
                } elseif (in_array('recursos_materiales', $userRole)) {
                    $query->where(function ($query) use ($usuarioLogueadoId) {
                        $query->whereIn('estado', [2, 5])
                            ->orWhere('id_solicitante', $usuarioLogueadoId);
                    });
                } else {
                    $query->where('id_solicitante', $usuarioLogueadoId);
                }
            });

        if ($request->filled('empresa')) {
            $query->whereHas('empresas', function ($q) use ($request) {
                $q->where('rfc', 'like', '%' . $request->empresa . '%');
            });
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
                case 'autorizado necesita cotizacion':
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

        if ($request->filled('fecha')) {
            $query->whereDate('fechasol', $request->fecha);
        }

        $query->orderBy('fechasol', 'desc');
        $requisiciones = $query->paginate(10);
        $requisiciones->appends(request()->except('page'));

        return view('requisiciones.historial', compact('requisiciones', 'userRole', 'usuariosDeJefe', 'usuarioLogueadoId'));
    }


    public function historialproductos(Request $request)
    {
        //dd($request->all());
        $query = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->where('requisicion.estado', 5);

        // Filtros adicionales
        if ($request->filled('descripcion')) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }
        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }
        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
        }
        if ($request->filled('justificacion')) {
            $query->where('justificacion', 'like', '%' . $request->justificacion . '%');
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
                case 'autorizado necesita cotizacion':
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

        if ($request->filled('inicio') && $request->filled('fin')) {
            $query->whereBetween('fechasol', [$request->inicio, $request->fin]);
        } elseif ($request->filled('inicio')) {
            $query->whereDate('fechasol', $request->inicio);
        } elseif ($request->filled('fin')) {
            $query->whereDate('fechasol', $request->fin);
        }


        // Clonamos la consulta para mantener el estado original
        $queryClone = clone $query;

        // Obtenemos todos los productos filtrados
        $productosfiltrados = $queryClone->get();

        // Obtenemos el total de productos
        $total_productos = $queryClone->selectRaw('SUM(productos_req.cantidad) as cantidad_total')->first();

        // Paginamos los productos requeridos
        $productosreq = $query->paginate(10);
        $productosreq->appends(request()->except('page'));

        return view('requisiciones.historialproductos', compact('productosreq', 'total_productos', 'productosfiltrados'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $usuarioLogueadoId = Auth::user()->id;

        $usuario = User::join('dato_usuario', 'dato_usuario.user_id', '=', 'user.id')
            ->join('area', 'area.idarea', 'dato_usuario.area_idarea')
            ->where('user.id', $usuarioLogueadoId)
            ->select('user.*', 'dato_usuario.*', 'area.*')
            ->first();

        $unidades = Unidad::get();
        $tipoinsumo = TipoInsumo::get();
        $empresas = Empresas::where('Id', 3)->first();

        if ($usuario) {
            return view('requisiciones.create', compact('usuario', 'unidades', 'tipoinsumo', 'empresas'));
        } else {
            return view('requisiciones.create')->with('error', 'Usuario no encontrado');
        }
    }

    public function ultimoFolioPorEmpresa($empresaId)
    {
        $ultimoFolio = DB::table('requisicion')
            ->join('productos_req', function ($join) {
                $join->on('requisicion.folio', '=', 'productos_req.folio')
                    ->on('requisicion.empresa', '=', 'productos_req.empresa');
            })
            ->where('requisicion.empresa', $empresaId)
            ->orderBy('requisicion.folio', 'desc')
            ->select('requisicion.folio')
            ->first();

        $nuevoFolio = $ultimoFolio ? $ultimoFolio->folio + 1 : 1;

        return  $nuevoFolio;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'solicitante' => 'required|string',
            'area' => 'required|string',
            'insumo' => 'required|integer',
            // 'empresa' => 'required|integer',
            'unidad' => 'required|array|min:1', // Verifica que haya al menos un artículo
            'descripcion' => 'required|array|min:1',
            'justificacion' => 'required|array|min:1',
            'cantidad' => 'required|array|min:1',
            'unidad.*' => 'required|string',
            'descripcion.*' => 'required|string',
            'justificacion.*' => 'required|string',
            'diagnostico.*' => 'mimes:jpeg,jpg,png,pdf,docx|max:2048',
            'cantidad.*' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $folio = $this->ultimoFolioPorEmpresa(3);
            $fechaSolicitud = Carbon::now();
            $semanaSolicitud = $fechaSolicitud->weekOfYear;
            //$rol = auth()->user()->roles->pluck('name')->all();
            $num_estado = 6;
            // Crear la nueva requisición
            $requisicion = Requisicion::create([
                'folio' => $folio,
                'solicitante' => $request->solicitante,
                'fechasol' => now(),
                'estado' => $num_estado,
                'empresa' => 3,
                'area' => $request->area,
                'semanasol' => $semanaSolicitud,
                'id_solicitante' => auth()->user()->id,
                'idtipo_insumo' => $request->insumo,
            ]);

            // Manejo de archivos


            if ($request->hasFile('diagnostico')) {
                $file = $request->file('diagnostico');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('/', $filename, 'ftp');

                $requisicion->diagnostico = $filePath;
                $requisicion->save();
            }

            // Guardar los productos de la requisición
            $guardadoAlMenosUno = false;
            foreach ($request->unidad as $index => $unidad) {
                $producto = ProductosReq::create([
                    'folio' => $folio, // Utilizar el folio generado
                    'empresa' => $requisicion->empresa,
                    'cantidad' => $request->cantidad[$index],
                    'unidad' => $unidad,
                    'descripcion' => $request->descripcion[$index],
                    'justificacion' => $request->justificacion[$index],
                ]);
                if ($producto) {
                    $guardadoAlMenosUno = true;
                }
            }
            if (!$guardadoAlMenosUno) {
                throw new \Exception('Debe agregar al menos un artículo.');
            }
            DB::commit();
            return redirect()->route('requisiciones-index')->with('success', "Requisición creada exitosamente con folio: $folio");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('requisiciones-index')->with('error', 'Hubo un error al crear la requisición: ' . $e->getMessage());
        }
    }

    //  public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'solicitante' => 'required|string',
    //         'area' => 'required|string',
    //         'insumo' => 'required|integer',
    //         // 'empresa' => 'required|integer',
    //         'unidad' => 'required|array|min:1', // Verifica que haya al menos un artículo
    //         'descripcion' => 'required|array|min:1',
    //         'justificacion' => 'required|array|min:1',
    //         'cantidad' => 'required|array|min:1',
    //         'unidad.*' => 'required|string',
    //         'descripcion.*' => 'required|string',
    //         'justificacion.*' => 'required|string',
    //         'cotizaciones.*' => 'required|mimes:jpeg,jpg,png,pdf,docx|max:2048',
    //         'cantidad.*' => 'required|integer|min:1'
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $folio = $this->ultimoFolioPorEmpresa(3);
    //         $fechaSolicitud = Carbon::now();
    //         $semanaSolicitud = $fechaSolicitud->weekOfYear;
    //         //$rol = auth()->user()->roles->pluck('name')->all();
    //         $num_estado = 6;
    //         // Crear la nueva requisición
    //         $requisicion = Requisicion::create([
    //             'folio' => $folio,
    //             'solicitante' => $request->solicitante,
    //             'fechasol' => now(),
    //             'estado' => $num_estado,
    //             'empresa' => 3,
    //             'area' => $request->area,
    //             'semanasol' => $semanaSolicitud,
    //             'id_solicitante' => auth()->user()->id,
    //             'idtipo_insumo' => $request->insumo,
    //         ]);

    //          // Manejo de archivos
    //         if ($request->hasFile('cotizaciones')) {
    //             foreach ($request->file('cotizaciones') as $file) {
    //                 // Guardar el archivo en el FTP
    //                 $filename = $file->getClientOriginalName(); // Obtener el nombre original del archivo
    //                 $filePath = $file->storeAs('/', $filename, 'ftp'); // Guardar en el FTP

    //                 // Registrar la cotización en la base de datos
    //                 Cotizacion::create([
    //                     'requisicion_id' => $requisicion->id,
    //                     'archivo' => $filePath,
    //                 ]);
    //             }
    //         }
    //         // Guardar los productos de la requisición
    //         $guardadoAlMenosUno = false;
    //         foreach ($request->unidad as $index => $unidad) {
    //             $producto = ProductosReq::create([
    //                 'folio' => $folio, // Utilizar el folio generado
    //                 'empresa' => $requisicion->empresa,
    //                 'cantidad' => $request->cantidad[$index],
    //                 'unidad' => $unidad,
    //                 'descripcion' => $request->descripcion[$index],
    //                 'justificacion' => $request->justificacion[$index],
    //             ]);
    //             if ($producto) {
    //                 $guardadoAlMenosUno = true;
    //             }
    //         }
    //         if (!$guardadoAlMenosUno) {
    //             throw new \Exception('Debe agregar al menos un artículo.');
    //         }
    //         DB::commit();
    //         return redirect()->route('requisiciones-index')->with('success', "Requisición creada exitosamente con folio: $folio");
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->route('requisiciones-index')->with('error', 'Hubo un error al crear la requisición: ' . $e->getMessage());
    //     }
    // }


    public function duplicar($id)
    {
        // Encontrar la requisición existente
        $requisicionOriginal = Requisicion::findOrFail($id);

        // Verificar si la requisición está en estado 'Entregado' (estado 5 en tu caso)
        if ($requisicionOriginal->estado != 5 && $requisicionOriginal->estado != 3) {
            return redirect()->route('requisiciones-index')->with('error', 'Solo las requisiciones entregadas se pueden duplicar.');
        }
        $usuarioLogueadoId = Auth::user()->id;

        $usuario = User::join('dato_usuario', 'dato_usuario.user_id', '=', 'user.id')
            ->join('area', 'area.idarea', '=', 'dato_usuario.area_idarea')
            ->where('user.id', $usuarioLogueadoId)
            ->select('area.nombre as area_nombre', 'user.name as usuario_nombre')
            ->first();

        // Crear un nuevo folio para la requisición duplicada
        $nuevoFolio = $this->ultimoFolioPorEmpresa($requisicionOriginal->empresa);

        // Duplicar la requisición (sin guardarla aún)
        $nuevaRequisicion = $requisicionOriginal->replicate();
        $nuevaRequisicion->folio = $nuevoFolio;
        $nuevaRequisicion->estado = 6; // Estado "Capturado" o el estado que desees para la nueva requisición
        $nuevaRequisicion->fechasol = now(); // Actualizar la fecha de solicitud

        // Obtener los productos relacionados de la requisición original
        $productosOriginales = ProductosReq::where('folio', $requisicionOriginal->folio)->get();

        $unidades = Unidad::get();
        $tipoinsumo = TipoInsumo::get();
        // Devolver una vista para editar la nueva requisición y los productos antes de guardarla
        return view('requisiciones.duplicar', compact('nuevaRequisicion', 'usuario', 'tipoinsumo', 'unidades', 'requisicionOriginal', 'productosOriginales'));
    }

    // Método para descargar la cotización
    public function downloadCotizacion($file)
    {

        $filePath = "/{$file}";

        if (Storage::disk('ftp')->exists($filePath)) {

            return Storage::disk('ftp')->download($filePath);
        } else {

            return redirect()->back()->with('error', 'El archivo no existe.');
        }
    }

    public function viewCotizacion($file)
    {

        $filePath = "/{$file}";

        if (Storage::disk('ftp')->exists($filePath)) {
            // Descargar el archivo temporalmente en el servidor local
            $tempFile = Storage::disk('local')->put("temp/{$file}", Storage::disk('ftp')->get($filePath));

            // Obtener la ruta completa del archivo temporal
            $tempFilePath = storage_path("app/temp/{$file}");

            // Devolver el archivo para mostrarlo en el navegador
            return response()->file($tempFilePath);
        } else {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }
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

        $cotizaciones = Cotizacion::where('requisicion_id', $id)->get();

        $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;
        $userRole = $usuarioLogueado->roles->pluck('name')->all();

        $id_requi = $id;

        //dd($requisiciones);

        return view('requisiciones.show', compact('requisiciones', 'productos', 'cotizaciones', 'userRole', 'usuarioLogueadoId', 'id_requi'));
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
        // dd($id);
        $requisicion = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')->findOrFail($id);
        $productos = ProductosReq::where('folio', $requisicion->folio)->where('empresa', $requisicion->empresa)->get();
        $unidades = Unidad::all();

        // Obtener las cotizaciones asociadas a la requisición
        $cotizaciones = Cotizacion::where('requisicion_id', $requisicion->id)->get();

        return view('requisiciones.edit', compact('requisicion', 'productos', 'unidades', 'cotizaciones'));
    }

    public function update(Request $request, $id)
    {
        // Validar datos de entrada
        $request->validate([
            'unidad.*' => 'required|string|max:255',
            'descripcion.*' => 'required|string|max:255',
            'justificacion.*' => 'required|string|max:255',
            'cantidad.*' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            // Buscar la requisición
            $requisicion = Requisicion::findOrFail($id);
            $requisicion->update($request->only(['solicitante', 'empresa', 'area']));

            // Eliminar productos antiguos y guardar nuevos
            ProductosReq::where('folio', $requisicion->folio)->delete();

            foreach ($request->unidad as $index => $unidad) {
                ProductosReq::create([
                    'folio' => $requisicion->folio,
                    'empresa' => $requisicion->empresa,
                    'cantidad' => $request->cantidad[$index],
                    'unidad' => $unidad,
                    'descripcion' => $request->descripcion[$index],
                    'justificacion' => $request->justificacion[$index],
                ]);
            }

            // Manejo del archivo diagnóstico
            if ($request->hasFile('diagnostico')) {
                // Eliminar archivo anterior si existe
                if ($requisicion->diagnostico) {
                    Storage::disk('ftp')->delete($requisicion->diagnostico);
                }

                // Guardar nuevo archivo
                $file = $request->file('diagnostico');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('/', $filename, 'ftp');

                // Actualizar en la base de datos
                $requisicion->update(['diagnostico' => $filePath]);
            }

            DB::commit();
            return redirect()->route('requisiciones-index', $id)
                ->with('success', 'Requisición actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('requisiciones-index', $id)
                ->with('error', 'Hubo un error al actualizar la requisición');
        }
    }

    // public function update(Request $request, $id)
    // {

    // if ($errors = $request->validate([
    //     'unidad.*' => 'required|string|max:255',
    //     'descripcion.*' => 'required|string|max:255',
    //     'justificacion.*' => 'required|string|max:255',
    //     'cantidad.*' => 'required|integer',
    //     'nueva_cotizacion.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:2048',
    //     'nuevas_cotizaciones.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:2048',
    // ]))


    //     DB::beginTransaction();

    //     try {
    //         $requisicion = Requisicion::findOrFail($id);
    //         $requisicion->update($request->only(['solicitante', 'empresa', 'area']));

    //         // Eliminar productos antiguos y guardar nuevos
    //         ProductosReq::where('folio', $requisicion->folio)->delete();

    //         // dd($request->all());
    //         foreach ($request->unidad as $index => $unidad) {
    //             ProductosReq::updateOrCreate([
    //                 'folio' => $requisicion->folio,
    //                 'empresa' => $requisicion->empresa,
    //                 'cantidad' => $request->cantidad[$index],
    //                 'unidad' => $unidad,
    //                 'descripcion' => $request->descripcion[$index],
    //                 'justificacion' => $request->justificacion[$index],
    //             ]);
    //         }

    //         // Reemplazar cotizaciones existentes
    //         if ($request->hasFile('nueva_cotizacion')) {
    //             foreach ($request->file('nueva_cotizacion') as $id => $file) {
    //                 $cotizacion = Cotizacion::find($id);
    //                 if ($cotizacion) {
    //                     Storage::disk('ftp')->delete($cotizacion->archivo);
    //                     $filename = $file->getClientOriginalName();
    //                     $filePath = $file->storeAs('/', $filename, 'ftp');
    //                     $cotizacion->update(['archivo' => $filePath]);
    //                 }
    //             }
    //         }

    //         // Subir nuevas cotizaciones
    //         if ($request->hasFile('nuevas_cotizaciones')) {
    //             foreach ($request->file('nuevas_cotizaciones') as $file) {
    //                 $filename = $file->getClientOriginalName();
    //                 $filePath = $file->storeAs('/', $filename, 'ftp');
    //                 Cotizacion::create([
    //                     'archivo' => $filePath,
    //                     'requisicion_id' => $requisicion->id,
    //                 ]);
    //             }
    //         }

    //         DB::commit();
    //         return redirect()->route('requisiciones-index', $id)
    //                         ->with('success', 'Requisición actualizada exitosamente');
    //     } catch (\Exception $e) {
    //         // dd($e);
    //         DB::rollBack();
    //         return redirect()->route('requisiciones-index', $id)
    //                         ->with('error', 'Hubo un error al actualizar la requisición');
    //     }
    // }

    public function autorizar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:requisicion,id',
            'estado' => 'required|string',
            'respuesta' => 'required|string',
            'fechares' => 'required|date',
            'semanres' => 'required|integer',
            'id_userautoriza' => 'required|exists:user,id',
        ], [
            // Mensajes personalizados para cada campo
            'id.required' => 'El campo ID de requisición es obligatorio.',
            'id.exists' => 'El ID de requisición no existe en la base de datos.',

            'estado.required' => 'El campo estado es obligatorio.',
            'estado.string' => 'El estado debe ser una cadena de texto válida.',

            'respuesta.required' => 'El campo respuesta es obligatorio.',
            'respuesta.string' => 'La respuesta debe ser una cadena de texto válida.',

            'fechares.required' => 'La fecha de respuesta es obligatoria.',
            'fechares.date' => 'La fecha de respuesta debe ser una fecha válida.',

            'semanres.required' => 'La semana de respuesta es obligatoria.',
            'semanres.integer' => 'La semana de respuesta debe ser un número entero.',

            'id_userautoriza.required' => 'El campo de usuario autorizado es obligatorio.',
            'id_userautoriza.exists' => 'El ID del usuario autorizado no es válido o no existe en la base de datos.',
        ]);

        $requisicion = Requisicion::find($request->id);


        $requisicion->fechares = $request->fechares;
        $requisicion->semanares = $request->semanres;
        $requisicion->estado = $request->estado;

        if ($request->estado == 4) {
            $requisicion->respuestapreautoriza = $request->respuesta;
            $requisicion->id_userpreautoriza = $request->id_userautoriza;

            if ($requisicion->idtipo_insumo == 2) {
                // Obtener el usuario con el rol 'autorizainsumos'
                $userContralor = User::whereHas('roles', function ($query) {
                    $query->where('name', 'contralor');
                })->first();
                if ($userContralor) {
                    // Enviar el correo al usuario con el rol 'autorizainsumos'
                    $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
                    Mail::to($userContralor->correo)->send(new RequisicionPreautorizada($requisicion, $datos));
                }
            } else {
                // Obtener el usuario con el rol 'autorizainsumos'
                $userAutorizainsumos = User::whereHas('roles', function ($query) {
                    $query->where('name', 'autorizainsumos');
                })->first();
                if ($userAutorizainsumos) {
                    $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
                    // Enviar el correo al usuario con el rol 'autorizainsumos'
                    Mail::to($userAutorizainsumos->correo)->send(new RequisicionPreautorizada($requisicion, $datos));
                }
            }
        } elseif ($request->estado == 2) {
            $requisicion->respuesta = $request->respuesta;
            $requisicion->id_userautoriza = $request->id_userautoriza;

            // Obtener el usuario con el rol 'autorizainsumos'
            $userAutorizainsumos = User::whereHas('roles', function ($query) {
                $query->where('name', 'recursos_materiales');
            })->first();

            if ($userAutorizainsumos) {
                $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
                // Enviar el correo al usuario con el rol 'autorizainsumos'
                Mail::to($userAutorizainsumos->correo)->send(new RequisicionAutorizada($requisicion, $datos));
            }
        } elseif ($request->estado == 9) {
            $requisicion->respuesta = $request->respuesta;
            $requisicion->id_userautoriza = $request->id_userautoriza;

            // Obtener el usuario con el rol 'rm'
            $userRM = User::whereHas('roles', function ($query) {
                $query->where('name', 'recursos_materiales');
            })->first();

            if ($userRM) {
                $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
                // Enviar el correo al usuario con el rol 'autorizainsumos'
                Mail::to($userRM->correo)->send(new RequisicionAutconCot($requisicion, $datos));
            }
        } else {
            $requisicion->respuesta = $request->respuesta;
        }

        $requisicion->save();

        return redirect()->route('requisiciones-index')->with('success', 'Requisición actualizada con éxito.');
    }


    public function generatePdf($requisitionId)
    {
        $requisicion = Requisicion::join('empresas', 'empresas.Id', 'requisicion.empresa')->join('user', 'user.id', 'requisicion.id_solicitante')->findOrFail($requisitionId);
        $productos = ProductosReq::where('folio', $requisicion->folio)->where('empresa', $requisicion->empresa)->get();
        $unidades = Unidad::all();
        $pdf = SnappyPdf::loadView('pdf.requisicion', compact('requisicion', 'productos', 'unidades'))
            ->setOption('enable-local-file-access', true);

        // $pdf = SnappyPdf::loadView('pdf.requisicion', compact('requisicion', 'productos', 'unidades' ));
        // Descarga el PDF
        return $pdf->download('requisicion.pdf');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        return redirect()->route('requisiciones-index')->with('success', 'Requisición actualizada con éxito.');
    }

    public function pendiente(Request $request)
    {
        $request->validate([
            'id_p' => 'required|exists:requisicion,id',
            'estado_pendiente' => 'required|string',
        ]);

        $rol = auth()->user()->roles->pluck('name')->all();
        $num_estado = $request->estado_pendiente;
        if (in_array('contralor', $rol)) {
            $num_estado = 2;
        } elseif (in_array('autorizainsumos', $rol)) {
            $num_estado = 2;
        } elseif (in_array('encargado', $rol)) {
            $num_estado = 4;
        } elseif (in_array('recursos_materiales', $rol)) {
            $num_estado = 4;
        }


        //dd($num_estado);
        $requisicion = Requisicion::find($request->id_p);
        $requisicion->estado = $num_estado;

        // Obtener el área del usuario autenticado
        $datoUsuario = DatoUsuario::where('user_id', auth()->user()->id)->first();
        $area = Area::where(
            'idarea',
            $datoUsuario->area_idarea
        )->first();
        // Obtener el correo del encargado del área
        $encargado = DatoUsuario::where('user_id', $area->id_encargado)->first();
        $id_user_actual = auth()->user()->id;
        //dd($encargado);
        if ($encargado && $encargado->user_id != $id_user_actual) {
            // Enviar el correo al encargado del área
            $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->join('user', 'user.id', 'area.id_encargado')->where('requisicion.id', $requisicion->id)->get();

            Mail::to($encargado->correo)->send(new
                RequisicionCreada($requisicion, $datos));
        }

        if ($num_estado == 4 && $requisicion->idtipo_insumo == 1) {
            $userAutorizainsumos = User::whereHas('roles', function ($query) {
                $query->where('name', 'autorizainsumos');
            })->first();

            $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
            // Enviar el correo al usuario con el rol 'autorizainsumos'
            Mail::to($userAutorizainsumos->correo)->send(new
                RequisicionPreautorizada($requisicion, $datos));
        } elseif ($num_estado == 4 && $requisicion->idtipo_insumo == 2) {

            $userAutorizainsumos = User::whereHas('roles', function ($query) {
                $query->where('name', 'contralor');
            })->first();

            $datos = ProductosReq::join('requisicion', 'requisicion.folio', 'productos_req.folio')->join('area', 'area.nombre', 'requisicion.area')->where('requisicion.id', $requisicion->id)->get();
            // Enviar el correo al usuario con el rol 'autorizainsumos'
            Mail::to($userAutorizainsumos->correo)->send(new
                RequisicionPreautorizada($requisicion, $datos));
        }


        $requisicion->save();
        return redirect()->route('requisiciones-index')->with(
            'success',
            'Requisición enviada con éxito.'
        );
    }



    public function exportexcel(Request $request)
    {
        //dd($request->all());
        $productosFiltrados = $request->productosFiltrados;
        $total = $request->totalProductos;
        $export = new HistorialProductosExcel($productosFiltrados, $total);

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'reporte.xlsx');
    }

    public function destroycoti($id)
    {
        $cotizacion = Cotizacion::find($id);
        if ($cotizacion) {
            // Eliminar archivo físico si es necesario
            Storage::disk('ftp')->delete('/cotizaciones/' . $cotizacion->archivo);
            // Eliminar cotización de la base de datos
            $cotizacion->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function updatePrecios(Request $request)
    {
        $costoUnidad = $request->input('costo_unidad');

        foreach ($costoUnidad as $productoId => $costo) {
            ProductosReq::where('id', $productoId)->update(['costo_unidad' => $costo]);
        }

        return response()->json(['message' => 'Precios actualizados correctamente.']);
    }
}
