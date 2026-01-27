<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\FechaVacacione;
use App\Models\VacacionesAcumulada;
use App\Models\DatoUsuario;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;


use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
//use Spatie\Permission\Traits\HasRoles;
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



    public function indexCalendario()
    {
        $user = auth()->user();

        // El iduser es el ID de la tabla users (Laravel)
        $iduser = $user->id;

        // Define el rol
        $rol = $user->role_id ?? 2; //

        $sam = $user->name;

        // Para ldapName, puedes obtenerlo de user o crear uno
        $ldapName = $user->name ?? 'Usuario';

        // Construir el resumen completo de vacaciones
        // $resumenVacaciones = $this->calcularResumenVacaciones($iduser);


        $resumenVacaciones = [
            'usuario' => $user->name . ' ' . ($user->apaterno ?? ''),
            'fecha_ingreso' => $user->fecha_ingreso ?? '2023-01-15', // Debería de tener este campo en el modelo DatoUsuario
            'antiguedad_anios' => 2, // Calcular esto automáticamente
            'antiguedad_meses' => 3,

            'anio_actual' => [
                'dias_totales' => 12,
                'dias_tomados' => 3,
                'dias_pendientes' => 9
            ],

            'historico' => [
                '2023' => [
                    'antiguedad_anios' => 1,
                    'dias_totales' => 12,
                    'dias_tomados' => 6,
                    'dias_pendientes' => 6
                ],
                '2024' => [
                    'antiguedad_anios' => 2,
                    'dias_totales' => 12,
                    'dias_tomados' => 3,
                    'dias_pendientes' => 9
                ]
            ],

            'total_general' => [
                'dias_totales' => 24,
                'dias_tomados' => 9,
                'dias_pendientes' => 15
            ]
        ];



        return view('vacaciones.calendario', compact('sam', 'ldapName', 'rol', 'iduser', 'resumenVacaciones'));
    }

    public function indexAgenda()
    {
        $user = auth()->user();

        // El iduser es el ID de la tabla users (Laravel)
        $iduser = $user->id;

        // Define el rol (temporalmente hasta configurar Spatie)
        // Si tienes Spatie instalado:
        // $rol = $user->hasRole('admin') ? 1 : 2;
        $rol = $user->role_id ?? 2; //

        $sam = $user->name;

        // Para ldapName, obtenerlo de user o crear uno
        $ldapName = $user->name ?? 'Usuario';

        return view('vacaciones.agenda', compact('sam', 'ldapName', 'rol', 'iduser'));
    }

    public function registrar(Request $request)
    {
        // Determinar desde qué vista viene la solicitud
        $esDesdeAgenda = $request->has('desde_agenda') || str_contains($request->header('referer'), '/agenda');

        $nombreuser = $request->input('nombre_user');
        $asunto = $request->input('asunto'); // Opcional
        $fechain = $request->input('fechain');
        $fechafin = $request->input('fechafin');
        $id = $request->input('id');
        $iduser = $request->input('iduserev');

        $iduser2 = intval($request->input('iduserev'));

        // Verifica si ya existe un evento en la fecha seleccionada
        // $eventoExistente = FechaVacacione::where('fecha_inicio', '<=', $fechafin)
        //     ->where('fecha_fin', '>=', $fechain)
        //     ->where('id', '!=', $id)
        //     ->count();

        // if ($eventoExistente > 0) {
        //     return response()->json(['msg' => 'Fecha ocupada', 'tipo' => 'warning']);
        // }

        // Obtén el evento existente desde la base de datos
        $eventoExistente = FechaVacacione::find($id);

        // Verifica si el evento existe
        if (!$eventoExistente) {
            // Si no existe, crea un nuevo evento
            $evento = new FechaVacacione();
            $evento->nombre_usuario = $nombreuser;
            $evento->asunto = $asunto; // Guardar asunto (puede ser null)
            $evento->fecha_inicio = $fechain;
            $evento->fecha_fin = $fechafin;


            $evento->iduser = $iduser;

            $evento->estatus = 3;  // Siempre como pendiente para nuevos eventos

            if ($iduser2 === 11) {
                $evento->color = '#1465bb'; // Color azul
            } else {
                $evento->color = '#808080'; // Gris para pendiente
            }

            $evento->save();

            return response()->json(['msg' => 'Evento agregado', 'tipo' => 'success']);
        }

        // Modificar evento existente
        $eventoExistente->asunto = $asunto; // Actualizar asunto (puede ser null)

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

    // Listar Agenda
    public function listarAgenda()
    {
        // Con relación eager loading si tienes la relación definida
        $eventos = FechaVacacione::with(['datoUsuario' => function ($query) {
            $query->select('user_id', 'nombre', 'apaterno', 'amaterno');
        }])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($evento) {
                $fechaInicio = new \DateTime($evento->fecha_inicio, new \DateTimeZone('America/Mexico_City'));
                $fechaFin = new \DateTime($evento->fecha_fin, new \DateTimeZone('America/Mexico_City'));

                // Ajuste para FullCalendar
                $fechaFin->modify('+1 day');

                $fechaCreacion = new \DateTime($evento->created_at, new \DateTimeZone('America/Mexico_City'));

                // Usar el nombre de datoUsuario si existe, si no usar nombre_usuario
                $nombreUsuario = $evento->datoUsuario
                    ? $evento->datoUsuario->nombre . ' ' . $evento->datoUsuario->apaterno
                    : $evento->nombre_usuario;

                // En agenda: Si hay asunto, mostrarlo. Si no, mostrar nombre.
                $titulo = $evento->asunto ?: $nombreUsuario;

                // Opcional: agregar nombre entre paréntesis si hay asunto
                if ($evento->asunto) {
                    $titulo .= " ($nombreUsuario)";
                }

                return [
                    'id' => $evento->id,
                    'title' => $nombreUsuario,
                    'Creado el ' => $fechaCreacion->format('d-m-Y H:i:s'),
                    'start' => $fechaInicio->format('Y-m-d\TH:i:s'),
                    'end' => $fechaFin->format('Y-m-d\TH:i:s'),
                    'color' => $evento->color,
                    'iduser' => $evento->iduser,
                    'estatus' => $evento->estatus,
                    'allDay' => true,
                    'extendedProps' => [
                        'nombre_usuario' => $nombreUsuario, // Nombre limpio
                        'asunto' => $evento->asunto, // Incluir asunto
                        'iduser' => $evento->iduser,
                        'estatus' => $evento->estatus,
                    ]
                ];
            });

        return response()->json($eventos);
    }

    // Listar Calendario
    public function listar()
    {
        $user = auth()->user();
        $userId = $user->id;
        $rol = $user->role_id ?? 2;

        // Depuración
        // \Log::info('Usuario solicitando eventos', [
        //     'user_id' => $userId,
        //     'rol' => $rol,
        //     'user_name' => $user->name
        // ]);

        // Si es usuario normal, solo sus eventos
        if ($rol == 2) {
            $eventos = FechaVacacione::where('iduser', $userId)
                ->orderBy('created_at', 'asc')
                ->get();
        }
        // Si es admin, todos los eventos de la BD
        else if ($rol == 1) {
            $eventos = FechaVacacione::orderBy('created_at', 'asc')->get();
        }
        // Por defecto, solo sus eventos
        else {
            $eventos = FechaVacacione::where('iduser', $userId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Transformar eventos para FullCalendar
        $eventosFormateados = $eventos->map(function ($evento) {
            $fechaInicio = new \DateTime($evento->fecha_inicio, new \DateTimeZone('America/Mexico_City'));
            $fechaFin = new \DateTime($evento->fecha_fin, new \DateTimeZone('America/Mexico_City'));
            $fechaFin->modify('+1 day');

            $fechaCreacion = new \DateTime($evento->created_at, new \DateTimeZone('America/Mexico_City'));

            // Obtener nombre del usuario
            // $nombreUsuario = $evento->nombre_usuario;

            // Obtener el nombre de datoUsuario si existe, si no usar nombre_usuario
            $nombreUsuario = $evento->datoUsuario
                ? $evento->datoUsuario->nombre . ' ' . $evento->datoUsuario->apaterno
                : $evento->nombre_usuario;

            return [
                'id' => $evento->id,
                'title' => $nombreUsuario,
                'Creado el ' => $fechaCreacion->format('d-m-Y H:i:s'),
                'start' => $fechaInicio->format('Y-m-d\TH:i:s'),
                'end' => $fechaFin->format('Y-m-d\TH:i:s'),
                'color' => $evento->color,
                'iduser' => $evento->iduser,
                'estatus' => $evento->estatus,
                'allDay' => true,
                'extendedProps' => [
                    'nombre_usuario' => $nombreUsuario,
                    'iduser' => $evento->iduser,
                    'estatus' => $evento->estatus,
                ]
            ];
        });

        return response()->json($eventosFormateados);
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

    // ==================== MÉTODOS PARA RESUMEN DE VACACIONES ====================


    // ==================== DISPONIBILIDAD ====================


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
