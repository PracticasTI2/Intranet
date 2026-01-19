<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use App\Models\FechaVacacione;


use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
//use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
//use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Arr;



class EventosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */




    // public function index()
    // {
    //     $user = Auth::user();
    //     $iduser = Auth::user()->id;

    //     // $rol = $user->roles->pluck('name')->all();
    //     $rol = 1;


    //     //     $rol = User::where('email', $user->samaccountname[0])
    //     //         ->join('model_has_roles', 'model_has_roles.model_id', '=', 'user.id')
    //     //         ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
    //     //         ->pluck('model_has_roles.role_id')
    //     //         ->first();

    //     //         $usuarioLogueadoId = Auth::user()->id;
    //     //         $userRole = $usuarioLogueadoId->roles->pluck('id')->all();
    //     // dd($userRole);
    //     // $iduser = User::where('email', $user->samaccountname[0])
    //     // ->pluck('user.id')
    //     // ->first();

    //     $eventos = FechaVacacione::orderBy('created_at', 'asc')->get();
    //     // $userRole = $user->roles();
    //     $sam = $user->name;

    //     if (isset($user->cn) && count($user->cn) > 0) {
    //         // Accede al primer valor del array "cn"
    //         $ldapName = $sam;
    //     } else {
    //         // Si no hay un valor "cn", establece un valor predeterminado o maneja la situación según sea necesario
    //         $ldapName = 'Nombre por defecto';
    //     }

    //     return view('vacaciones.index', compact('eventos', 'ldapName', 'sam', 'user', 'rol', 'iduser'));
    // }

    // public function index()
    // {
    //     $user = auth()->user();



    //     // $rol = User::where('email', $user->samaccountname[0])
    //     //     ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
    //     //     ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
    //     //     ->pluck('model_has_roles.role_id')
    //     //     ->first();

    //     // $iduser = User::where('email', $user->samaccountname[0])
    //     //     ->pluck('users.id')
    //     //     ->first();

    //     // $eventos = FechaVacacione::orderBy('created_at', 'asc')->get();

    //     // $sam = $user->samaccountname[0];

    //     // if (isset($user->cn) && count($user->cn) > 0) {
    //     //     // Accede al primer valor del array "cn"
    //     //     $ldapName = $user->cn[0];
    //     // } else {
    //     //     // Si no hay un valor "cn", establece un valor predeterminado o maneja la situación según sea necesario
    //     //     $ldapName = 'Nombre por defecto';
    //     // }


    //     // return view('vacaciones.index', compact('eventos', 'ldapName', 'sam', 'user', 'rol', 'iduser'));

    //     return view('vacaciones.index', compact('user'));
    //     // return view('vacaciones.index');

    // }


    public function index()
    {
        $user = auth()->user();

        $iduser = $user->id;

        // Si todavía no manejas roles bien, deja un default:
        // (mejor luego lo ligamos a Spatie)
        $rol = $user->hasRole('admin') ? 1 : 2;  // ejemplo
        // o temporal:
        // $rol = 2;

        $sam = $user->name; // lo estás usando en la vista
        $ldapName = $user->name ?? 'Nombre por defecto';

        return view('vacaciones.index', compact('sam', 'ldapName', 'rol', 'iduser'));
    }





    public function registrar(Request $request)
    {

        $nombreuser = $request->input('nombre_user');

        $fechain = $request->input('fechain');
        $fechafin = $request->input('fechafin');
        $id = $request->input('id');
        $iduser = $request->input('iduserev');

        $iduser2 = intval($request->input('iduserev'));

        // Verifica si ya existe un evento en la fecha seleccionada
        $eventosExistente = FechaVacacione::where('fecha_inicio', '<=', $fechafin)
            ->where('fecha_fin', '>=', $fechain)
            ->where('id', '!=', $id)
            ->count();

        // if ($eventosExistente > 0) {
        //     return response()->json(['msg' => 'Fecha ocupada', 'tipo' => 'warning']);
        // }

        // Obtén el evento existente desde la base de datos
        $eventoExistente = FechaVacacione::find($id);

        // Verifica si el evento existe
        if (!$eventoExistente) {
            // Si no existe, crea un nuevo evento
            $evento = new FechaVacacione();
            $evento->nombre_usuario = $nombreuser;
            $evento->fecha_inicio = $fechain;
            $evento->fecha_fin = $fechafin;


            $evento->iduser = $iduser;

            $evento->estatus = 3;

            if ($iduser2 === 11) {
                $evento->color = '#1465bb';
            } else {
                $evento->color = '#808080';
            }

            $evento->save();

            return response()->json(['msg' => 'Evento agregado', 'tipo' => 'success']);
        }

        // Verifica si las fechas han cambiado
        if ($eventoExistente->fecha_inicio != $fechain || $eventoExistente->fecha_fin != $fechafin) {
            // Realiza la lógica de verificación de disponibilidad aquí
            $eventosSolapados = FechaVacacione::where('id', '!=', $id)
                ->where(function ($query) use ($fechain, $fechafin) {
                    $query->where(function ($q) use ($fechain, $fechafin) {
                        $q->where('fecha_inicio', '<=', $fechain)
                            ->where('fecha_fin', '>=', $fechain);
                    })->orWhere(function ($q) use ($fechain, $fechafin) {
                        $q->where('fecha_inicio', '>=', $fechain)
                            ->where('fecha_inicio', '<=', $fechafin);
                    });
                })
                ->count();

            // if ($eventosSolapados > 0) {
            //     return response()->json(['msg' => 'Fecha ocupada', 'tipo' => 'warning']);
            // }

            // Si las fechas han cambiado y no hay solapamientos, actualiza el evento
            $eventoExistente->fecha_inicio = $fechain;
            $eventoExistente->fecha_fin = $fechafin;

            $eventoExistente->save();

            return response()->json(['msg' => 'Evento modificado', 'tipo' => 'success']);
        }

        // Si las fechas no han cambiado, retorna un mensaje de 'Fecha disponible'
        return response()->json(['msg' => 'Fecha disponible', 'tipo' => 'success']);
    }


    public function listar()
    {
        $eventos = FechaVacacione::orderBy('created_at', 'asc')->get()->map(function ($evento) {
            $fechaInicio = new \DateTime($evento->fecha_inicio, new \DateTimeZone('America/Mexico_City'));
            $fechaFin = new \DateTime($evento->fecha_fin, new \DateTimeZone('America/Mexico_City'));

            // Ajusta la fecha de fin para que sea inclusive
            $fechaFin->modify('+1 day');

            $fechaCreacion = new \DateTime($evento->created_at, new \DateTimeZone('America/Mexico_City'));

            return [
                'id' => $evento->id,
                'title' => $evento->nombre_usuario . ' - Creado el ' . $fechaCreacion->format('d-m-Y H:i:s'),
                'start' => $fechaInicio->format('Y-m-d\TH:i:s'),
                'end' => $fechaFin->format('Y-m-d\TH:i:s'),
                'color' => $evento->color,
                'iduser' => $evento->iduser,
                'estatus' => $evento->estatus,
                'allDay' => true,
                // Añador más propiedades si es necesario para el formato de FullCalendar
            ];
        });

        return response()->json($eventos);
    }



    public function eliminarEvento($id)
    {
        try {

            // Obtén el evento por su ID
            $evento = FechaVacacione::where('id', $id)->first();

            if (!$evento) {
                // El evento no existe
                return response()->json(['msg' => 'Evento no encontrado', 'estado' => false, 'tipo' => 'danger']);
            }

            // Elimina el evento
            $evento->delete();

            return response()->json(['msg' => 'Evento eliminado', 'estado' => true, 'tipo' => 'success']);
        } catch (\Exception $e) {
            // Maneja cualquier excepción que pueda ocurrir
            return response()->json(['msg' => 'Error al eliminar el evento', 'estado' => false, 'tipo' => 'danger']);
        }
    }
    // public function drag()
    // {
    //     if (isset($_POST)) {
    //         if (empty($_POST['id']) || empty($_POST['start'])) {
    //             $msg = array('msg' => 'Todo los campos son requeridos', 'estado' => false, 'tipo' => 'danger');
    //         } else {
    //             $start = $_POST['start'];
    //             $id = $_POST['id'];
    //             $data = $this->model->dragOver($start, $id);
    //             if ($data == 'ok') {
    //                 $msg = array('msg' => 'Evento Modificado', 'estado' => true, 'tipo' => 'success');
    //             } else {
    //                 $msg = array('msg' => 'Error al Modificar', 'estado' => false, 'tipo' => 'danger');
    //             }
    //         }
    //         echo json_encode($msg);
    //     }
    //     die();
    // }



    public function verificarDisponibilidad(Request $request)
    {
        try {
            $fechain = $request->input('fechain');
            $fechafin = $request->input('fechafin');

            $evento = FechaVacacione::where(function ($query) use ($fechain, $fechafin) {
                $query->where('fecha_inicio', '<=', $fechafin)
                    ->where('fecha_fin', '>=', $fechain);
            })->count();

            if ($evento > 0) {
                return response()->json(['msg' => 'Algunos días del rango ya están ocupados. Elija otros días.', 'tipo' => 'warning']);
            } else {
                return response()->json(['msg' => 'El rango de fechas está disponible', 'tipo' => 'success']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function autorizar(Request $request, $id)
    {
        $autorizacion = FechaVacacione::find($id);

        $iduser = $autorizacion->iduser;

        if ($request->input('confirmado')) {
            // Si el usuario confirma, actualiza el estatus y el color
            $autorizacion->estatus = 1;

            if ($iduser === 153 || $iduser === 127 || $iduser === 157) {
                $autorizacion->color = '#276621';
            } else {
                $autorizacion->color = '#7ACC0E';
            }

            $msg = 'Evento autorizado';
            $tipo = 'success';
        } else {
            // Si el usuario cancela, actualiza el estatus y el color
            $autorizacion->estatus = 2;

            $autorizacion->color = '#FF0000'; // Rojo
            $msg = 'Evento no autorizado';
            $tipo = 'error';
        }

        $autorizacion->save();

        return response()->json(['msg' => $msg, 'estado' => true, 'tipo' => $tipo]);
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
}
