<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FechaVacacione;
use App\Models\DatoUsuario;
use App\Models\VacacionesAcumulada;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventosController extends Controller
{
    // Método principal para mostrar el calendario con resumen

    public function indexCalendario()
    {
        $user = auth()->user();
        $iduser = $user->id;
        $rol = $user->role_id ?? 2;
        $sam = $user->name;
        $ldapName = $user->name ?? 'Usuario';

        // Obtener resumen de vacaciones
        $resumenVacaciones = $this->obtenerResumenVacaciones($iduser);

        return view('vacaciones.calendario', compact('sam', 'ldapName', 'rol', 'iduser', 'resumenVacaciones'));
    }

    // ========== MÉTODOS PARA CÁLCULO DE VACACIONES ==========

    // Calcular días de vacaciones según Artículo 76 LFT
    private function calcularDiasVacacionesLFT($fechaIngreso, $anioCalcular = null): int
    {
        if (!$fechaIngreso) return 0;

        $ingreso = Carbon::parse($fechaIngreso);
        $fechaReferencia = $anioCalcular ? Carbon::create($anioCalcular, 12, 31) : Carbon::now(); // ultimo día del año a calcular (12 = diciembre, 31 = día 31)

        // Verificar si tiene al menos 6 meses trabajados
        $mesesTrabajados = $ingreso->diffInMonths($fechaReferencia, false);
        if ($mesesTrabajados < 6) return 0;

        // Calcular años completos trabajados
        $aniosCompletos = $ingreso->diffInYears($fechaReferencia, false);

        // Menos de 1 año pero más de 6 meses
        if ($aniosCompletos < 1) return 12;

        // Tabla LFT
        return match ($aniosCompletos) {
            1 => 12,
            2 => 14,
            3 => 16,
            4 => 18,
            5 => 20,
            6, 7, 8, 9 => 22,
            10, 11, 12, 13, 14 => 24,
            15, 16, 17, 18, 19 => 26,
            20 => 28,
            default => 28 + floor(($aniosCompletos - 20) / 5) * 2 // +2 días cada 5 años después de 20
        };
    }

    /**
     * Calcular antigüedad exacta
     */
    private function calcularAntiguedad($fechaIngreso): array
    {
        if (!$fechaIngreso) return ['anios' => 0, 'meses' => 0, 'dias' => 0];

        $diferencia = Carbon::now()->diff(Carbon::parse($fechaIngreso));

        return [
            'anios' => $diferencia->y,
            'meses' => $diferencia->m,
            'dias' => $diferencia->d
        ];
    }

    /**
     * Calcular antigüedad para un año específico
     */
    private function calcularAntiguedadParaAnio($userId, $anio): int
    {
        $datoUsuario = DatoUsuario::where('user_id', $userId)->first();
        if (!$datoUsuario || !$datoUsuario->ingreso) return 0;

        $ingreso = Carbon::parse($datoUsuario->ingreso);
        $finAnio = Carbon::create($anio, 12, 31);

        return max(0, $ingreso->diffInYears($finAnio, false));
    }

    /**
     * Calcular días hábiles entre dos fechas (sin fines de semana)
     */
    private function calcularDiasHabilesEntreFechas($inicio, $fin): int
    {
        $inicio = Carbon::parse($inicio);
        $fin = Carbon::parse($fin);

        $dias = 0;
        $fechaActual = $inicio->copy();

        while ($fechaActual <= $fin) {
            if (!$fechaActual->isWeekend()) {
                $dias++;
            }
            $fechaActual->addDay();
        }

        return $dias;
    }

    /**
     * Calcular días tomados para un año específico
     */
    private function calcularDiasTomadosAnio($userId, $anio): int
    {
        $vacaciones = FechaVacacione::where('iduser', $userId)
            ->whereYear('fecha_inicio', $anio)
            ->autorizadas()
            ->get();

        $totalDias = 0;
        foreach ($vacaciones as $vacacion) {
            $totalDias += $this->calcularDiasHabilesEntreFechas(
                $vacacion->fecha_inicio,
                $vacacion->fecha_fin
            );
        }

        return $totalDias;
    }

    // ========== MÉTODOS PARA OBTENER RESUMEN ==========

    private function obtenerResumenVacaciones($userId): array
    {
        try {
            $datoUsuario = DatoUsuario::where('user_id', $userId)->first();
            if (!$datoUsuario) return $this->estructuraResumenVacia();

            $anioActual = date('Y');
            $fechaIngreso = $datoUsuario->ingreso;
            if (!$fechaIngreso) return $this->estructuraResumenVacia($datoUsuario);

            // Siempre calcular en tiempo real
            $resumen = $this->calcularResumenEnTiempoReal($userId, $datoUsuario, $anioActual);

            // Guardar en tabla acumulada
            $this->guardarEnTablaAcumulada($userId, $anioActual, $resumen);

            return $resumen;
        } catch (\Exception $e) {
            Log::error('Error obteniendo resumen: ' . $e->getMessage());
            return $this->estructuraResumenVacia();
        }
    }

    /**
     * Obtener de tabla acumulada
     */
    private function obtenerDeTablaAcumulada($userId, $anioActual): ?array
    {
        $acumulada = VacacionesAcumulada::where('user_id', $userId)
            ->where('anio', $anioActual)
            ->first();

        if (!$acumulada) return null;

        $datoUsuario = DatoUsuario::where('user_id', $userId)->first();
        $antiguedad = $this->calcularAntiguedad($datoUsuario->ingreso);

        return [
            'usuario' => trim($datoUsuario->nombre . ' ' . ($datoUsuario->apaterno ?? '') . ' ' . ($datoUsuario->amaterno ?? '')),
            'fecha_ingreso' => $datoUsuario->ingreso,
            'antiguedad_anios' => $antiguedad['anios'],
            'antiguedad_meses' => $antiguedad['meses'],
            'antiguedad_dias' => $antiguedad['dias'],
            'anio_actual' => [
                'dias_totales' => $acumulada->dias_totales,
                'dias_tomados' => $acumulada->dias_tomados,
                'dias_pendientes' => $acumulada->dias_pendientes
            ],
            'historico' => $this->obtenerHistoricoAcumulado($userId),
            'total_general' => $this->calcularTotalesGenerales($userId)
        ];
    }

    /**
     * Calcular resumen en tiempo real
     */
    private function calcularResumenEnTiempoReal($userId, $datoUsuario, $anioActual): array
    {
        $antiguedad = $this->calcularAntiguedad($datoUsuario->ingreso);
        $diasCorrespondientes = $this->calcularDiasVacacionesLFT($datoUsuario->ingreso, $anioActual);
        $diasTomados = $this->calcularDiasTomadosAnio($userId, $anioActual);
        $diasPendientes = max(0, $diasCorrespondientes - $diasTomados);

        return [
            'usuario' => trim($datoUsuario->nombre . ' ' . ($datoUsuario->apaterno ?? '') . ' ' . ($datoUsuario->amaterno ?? '')),
            'fecha_ingreso' => $datoUsuario->ingreso,
            'antiguedad_anios' => $antiguedad['anios'],
            'antiguedad_meses' => $antiguedad['meses'],
            'antiguedad_dias' => $antiguedad['dias'],
            'anio_actual' => [
                'dias_totales' => $diasCorrespondientes,
                'dias_tomados' => $diasTomados,
                'dias_pendientes' => $diasPendientes
            ],
            'historico' => $this->calcularHistorico($userId, $datoUsuario->ingreso),
            'total_general' => $this->calcularTotalesGenerales($userId)
        ];
    }

    /**
     * Guardar en tabla acumulada
     */
    private function guardarEnTablaAcumulada($userId, $anio, $resumen): void
    {
        try {
            VacacionesAcumulada::updateOrCreate(
                [
                    'user_id' => $userId,
                    'anio' => $anio
                ],
                [
                    'dias_totales' => $resumen['anio_actual']['dias_totales'],
                    'dias_tomados' => $resumen['anio_actual']['dias_tomados'],
                    'dias_pendientes' => $resumen['anio_actual']['dias_pendientes'],
                    'fecha_corte' => Carbon::now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error guardando en tabla acumulada: ' . $e->getMessage());
        }
    }

    /**
     * Obtener histórico acumulado
     */
    private function obtenerHistoricoAcumulado($userId): array
    {
        $historico = [];
        $anioActual = date('Y');

        $acumuladas = VacacionesAcumulada::where('user_id', $userId)
            ->where('anio', '<', $anioActual)
            ->orderBy('anio', 'desc')
            ->limit(5)
            ->get();

        foreach ($acumuladas as $acumulada) {
            $historico[$acumulada->anio] = [
                'antiguedad_anios' => $this->calcularAntiguedadParaAnio($userId, $acumulada->anio),
                'dias_totales' => $acumulada->dias_totales,
                'dias_tomados' => $acumulada->dias_tomados,
                'dias_pendientes' => $acumulada->dias_pendientes
            ];
        }

        return $historico;
    }

    /**
     * Calcular histórico
     */
    private function calcularHistorico($userId, $fechaIngreso): array
    {
        $anioActual = date('Y');
        $anioIngreso = Carbon::parse($fechaIngreso)->year;
        $historico = [];

        // Últimos 5 años (excluyendo el actual)
        $anios = range(max($anioIngreso, $anioActual - 5), $anioActual - 1);

        foreach ($anios as $anio) {
            if ($anio < $anioActual) {
                $diasCorrespondientes = $this->calcularDiasVacacionesLFT($fechaIngreso, $anio);
                $diasTomados = $this->calcularDiasTomadosAnio($userId, $anio);

                $historico[$anio] = [
                    'antiguedad_anios' => max(0, $anio - $anioIngreso),
                    'dias_totales' => $diasCorrespondientes,
                    'dias_tomados' => $diasTomados,
                    'dias_pendientes' => max(0, $diasCorrespondientes - $diasTomados)
                ];
            }
        }

        return $historico;
    }

    /**
     * Calcular totales generales
     */
    private function calcularTotalesGenerales($userId): array
    {
        $acumuladas = VacacionesAcumulada::where('user_id', $userId)->get();

        $totales = [
            'dias_totales' => 0,
            'dias_tomados' => 0,
            'dias_pendientes' => 0
        ];

        foreach ($acumuladas as $acumulada) {
            $totales['dias_totales'] += $acumulada->dias_totales;
            $totales['dias_tomados'] += $acumulada->dias_tomados;
            $totales['dias_pendientes'] += $acumulada->dias_pendientes;
        }

        return $totales;
    }

    /**
     * Estructura vacía para resumen
     */
    private function estructuraResumenVacia($datoUsuario = null): array
    {
        $nombre = $datoUsuario
            ? trim($datoUsuario->nombre . ' ' . ($datoUsuario->apaterno ?? '') . ' ' . ($datoUsuario->amaterno ?? ''))
            : 'Usuario';

        return [
            'usuario' => $nombre,
            'fecha_ingreso' => null,
            'antiguedad_anios' => 0,
            'antiguedad_meses' => 0,
            'antiguedad_dias' => 0,
            'anio_actual' => [
                'dias_totales' => 0,
                'dias_tomados' => 0,
                'dias_pendientes' => 0
            ],
            'historico' => [],
            'total_general' => [
                'dias_totales' => 0,
                'dias_tomados' => 0,
                'dias_pendientes' => 0
            ]
        ];
    }

    /**
     * ========== MÉTODOS PARA EL CALENDARIO ==========
     */

    /**
     * Listar eventos para el calendario
     */
    public function listar()
    {
        $user = auth()->user();
        $userId = $user->id;
        $rol = $user->role_id ?? 2;

        // Si es usuario normal, solo sus eventos
        if ($rol == 2) {
            $eventos = FechaVacacione::where('iduser', $userId)->get();
        } else {
            // Si es admin, todos los eventos
            $eventos = FechaVacacione::all();
        }

        // Transformar eventos para FullCalendar
        $eventosFormateados = $eventos->map(function ($evento) {
            $inicio = Carbon::parse($evento->fecha_inicio);
            $fin = Carbon::parse($evento->fecha_fin)->addDay(); // FullCalendar necesita +1 día

            return [
                'id' => $evento->id,
                'title' => $evento->nombre_usuario,
                'start' => $inicio->format('Y-m-d\TH:i:s'),
                'end' => $fin->format('Y-m-d\TH:i:s'),
                'color' => $evento->color ?: $evento->obtenerColorEstatus(),
                'iduser' => $evento->iduser,
                'estatus' => $evento->estatus,
                'allDay' => true,
                'extendedProps' => [
                    'nombre_usuario' => $evento->nombre_usuario,
                    'iduser' => $evento->iduser,
                    'estatus' => $evento->estatus,
                ]
            ];
        });

        return response()->json($eventosFormateados);
    }

    /**
     * Registrar o modificar vacaciones
     */
    public function registrar(Request $request)
    {
        $nombreuser = $request->input('nombre_user');
        $asunto = $request->input('asunto', 'Vacaciones');
        $fechain = $request->input('fechain');
        $fechafin = $request->input('fechafin');
        $id = $request->input('id');
        $iduser = $request->input('iduserev');

        // ========== VALIDAR LÍMITES DE VACACIONES ==========
        if (!$id) { // Solo validar para nuevas solicitudes
            $diasSolicitados = $this->calcularDiasHabilesEntreFechas($fechain, $fechafin);
            $resumen = $this->obtenerResumenVacaciones($iduser);

            $diasDisponibles = $resumen['anio_actual']['dias_pendientes'] ?? 0;
            $diasTomados = $resumen['anio_actual']['dias_tomados'] ?? 0;
            $diasTotales = $resumen['anio_actual']['dias_totales'] ?? 0;

            // Mensajes personalizados según la situación
            if ($diasDisponibles <= 0) {
                return response()->json([
                    'msg' => "<strong>Ya no tienes días de vacaciones disponibles</strong><br>
                     Has utilizado tus <strong>{$diasTotales} días</strong> correspondientes para este año.<br>
                     <small>Si necesitas días adicionales, contacta a Recursos Humanos.</small>",
                    'tipo' => 'error',
                    'dias_disponibles' => $diasDisponibles,
                    'dias_tomados' => $diasTomados,
                    'dias_totales' => $diasTotales,
                    'agotado' => true
                ]);
            }

            if ($diasSolicitados > $diasDisponibles) {
                $excedente = $diasSolicitados - $diasDisponibles;

                return response()->json([
                    'msg' => "<strong>Límite de vacaciones excedido</strong><br>
                     Días disponibles: <strong class='text-success'>{$diasDisponibles}</strong><br>
                     Días solicitados: <strong class='text-warning'>{$diasSolicitados}</strong><br>
                     <span class='text-danger'>Excedente: {$excedente} días</span><br>
                     <small>Por favor, ajusta tu solicitud a los días disponibles.</small>",
                    'tipo' => 'warning',
                    'dias_disponibles' => $diasDisponibles,
                    'dias_solicitados' => $diasSolicitados,
                    'dias_tomados' => $diasTomados,
                    'dias_totales' => $diasTotales,
                    'excedente' => $excedente
                ]);
            }
        }
        // ========== FIN VALIDACIÓN ==========

        // Buscar evento existente
        $eventoExistente = FechaVacacione::find($id);

        if (!$eventoExistente) {
            // Crear nuevo evento
            $evento = new FechaVacacione();
            $evento->nombre_usuario = $nombreuser;
            $evento->asunto = $asunto;
            $evento->fecha_inicio = $fechain;
            $evento->fecha_fin = $fechafin;
            $evento->iduser = $iduser;
            $evento->estatus = 3; // Pendiente

            // Asignar color según usuario específico
            $evento->color = (intval($iduser) === 11) ? '#1465bb' : '#808080';

            $evento->save();

            return response()->json(['msg' => 'Solicitud de vacaciones registrada', 'tipo' => 'success']);
        }

        // Modificar evento existente (solo si está pendiente)
        if ($eventoExistente->estatus !== 3) {
            return response()->json(['msg' => 'No se puede modificar una solicitud ya procesada', 'tipo' => 'warning']);
        }

        $eventoExistente->asunto = $asunto;
        $eventoExistente->fecha_inicio = $fechain;
        $eventoExistente->fecha_fin = $fechafin;
        $eventoExistente->save();

        return response()->json(['msg' => 'Solicitud de vacaciones actualizada', 'tipo' => 'success']);
    }

    /**
     * Eliminar evento
     */
    public function eliminarEvento($id)
    {
        try {
            $evento = FechaVacacione::find($id);

            if (!$evento) {
                return response()->json(['msg' => 'Solicitud no encontrada', 'estado' => false, 'tipo' => 'danger']);
            }

            // Verificar que solo se puedan eliminar solicitudes pendientes
            if ($evento->estatus !== 3) {
                return response()->json(['msg' => 'No se puede eliminar una solicitud ya procesada', 'estado' => false, 'tipo' => 'warning']);
            }

            $evento->delete();
            return response()->json(['msg' => 'Solicitud eliminada', 'estado' => true, 'tipo' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error eliminando evento: ' . $e->getMessage());
            return response()->json(['msg' => 'Error al eliminar la solicitud', 'estado' => false, 'tipo' => 'danger']);
        }
    }

    /**
     * Autorizar o rechazar vacaciones
     */
    public function autorizar(Request $request, $id)
    {
        try {
            $autorizacion = FechaVacacione::find($id);

            if (!$autorizacion) {
                return response()->json(['msg' => 'Solicitud no encontrada', 'estado' => false, 'tipo' => 'danger']);
            }

            // Verificar que solo se puedan procesar solicitudes pendientes
            if ($autorizacion->estatus !== 3) {
                return response()->json(['msg' => 'Esta solicitud ya fue procesada', 'estado' => false, 'tipo' => 'warning']);
            }

            $iduser = $autorizacion->iduser;

            if ($request->input('confirmado')) {
                // Autorizar
                $autorizacion->estatus = 1;
                $autorizacion->color = in_array($iduser, [153, 127, 157]) ? '#276621' : '#7ACC0E';
                $msg = 'Solicitud autorizada';
                $tipo = 'success';

                // Actualizar acumulados (se hará automáticamente por el evento del modelo)
            } else {
                // Rechazar
                $autorizacion->estatus = 2;
                $autorizacion->color = '#FF0000';
                $msg = 'Solicitud rechazada';
                $tipo = 'error';
            }

            $autorizacion->save();

            // Actualizar resumen en tabla acumulada
            $this->actualizarAcumuladosUsuario($iduser, date('Y'));

            return response()->json(['msg' => $msg, 'estado' => true, 'tipo' => $tipo]);
        } catch (\Exception $e) {
            Log::error('Error autorizando vacaciones: ' . $e->getMessage());
            return response()->json(['msg' => 'Error al procesar la solicitud', 'estado' => false, 'tipo' => 'danger']);
        }
    }

    /**
     * Actualizar acumulados para un usuario y año
     */
    private function actualizarAcumuladosUsuario($userId, $anio): void
    {
        try {
            $datoUsuario = DatoUsuario::where('user_id', $userId)->first();
            if (!$datoUsuario || !$datoUsuario->ingreso) return;

            $diasTotales = $this->calcularDiasVacacionesLFT($datoUsuario->ingreso, $anio);
            $diasTomados = $this->calcularDiasTomadosAnio($userId, $anio);
            $diasPendientes = max(0, $diasTotales - $diasTomados);

            VacacionesAcumulada::updateOrCreate(
                [
                    'user_id' => $userId,
                    'anio' => $anio
                ],
                [
                    'dias_totales' => $diasTotales,
                    'dias_tomados' => $diasTomados,
                    'dias_pendientes' => $diasPendientes,
                    'fecha_corte' => Carbon::now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error actualizando acumulados: ' . $e->getMessage());
        }
    }

    /**
     * Verificar disponibilidad de fechas
     */
    public function verificarDisponibilidad(Request $request)
    {
        try {
            $fechain = $request->input('fechain');
            $fechafin = $request->input('fechafin');
            $id = $request->input('id');

            $evento = FechaVacacione::where('id', '!=', $id)
                ->where(function ($query) use ($fechain, $fechafin) {
                    $query->whereBetween('fecha_inicio', [$fechain, $fechafin])
                        ->orWhereBetween('fecha_fin', [$fechain, $fechafin])
                        ->orWhere(function ($q) use ($fechain, $fechafin) {
                            $q->where('fecha_inicio', '<=', $fechain)
                                ->where('fecha_fin', '>=', $fechafin);
                        });
                })
                ->exists();

            if ($evento) {
                return response()->json(['msg' => 'Algunos días del rango ya están ocupados. Elija otros días.', 'tipo' => 'warning']);
            } else {
                return response()->json(['msg' => 'El rango de fechas está disponible', 'tipo' => 'success']);
            }
        } catch (\Exception $e) {
            Log::error('Error verificando disponibilidad: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Método para validar que no incluya fines de semana
    private function validarFinesDeSemana($fechain, $fechafin): array
    {
        $inicio = Carbon::parse($fechain);
        $fin = Carbon::parse($fechafin);
        $errores = [];

        // Verificar si la fecha de inicio es fin de semana
        if ($inicio->isWeekend()) {
            $errores[] = "La fecha de inicio ({$inicio->format('d/m/Y')}) es " .
                ($inicio->isSaturday() ? 'sábado' : 'domingo') .
                ". Por favor selecciona un día hábil.";
        }

        // Verificar si la fecha final es fin de semana
        if ($fin->isWeekend()) {
            $errores[] = "La fecha final ({$fin->format('d/m/Y')}) es " .
                ($fin->isSaturday() ? 'sábado' : 'domingo') .
                ". Por favor selecciona un día hábil.";
        }

        // Verificar si hay fines de semana en el rango (opcional)
        $fechaActual = $inicio->copy();
        $finesDeSemanaEnRango = [];

        while ($fechaActual <= $fin) {
            if ($fechaActual->isWeekend()) {
                $finesDeSemanaEnRango[] = $fechaActual->format('d/m/Y') .
                    " (" . ($fechaActual->isSaturday() ? 'Sáb' : 'Dom') . ")";
            }
            $fechaActual->addDay();
        }

        if (!empty($finesDeSemanaEnRango)) {
            $errores[] = "El rango seleccionado incluye " . count($finesDeSemanaEnRango) .
                " día(s) de fin de semana: " . implode(', ', $finesDeSemanaEnRango);
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'fines_semana_en_rango' => $finesDeSemanaEnRango
        ];
    }


    // ========== MÉTODOS PARA AGENDA ==========

    public function indexAgenda()
    {
        $user = auth()->user();
        $iduser = $user->id;
        $rol = $user->role_id ?? 2;
        $sam = $user->name;
        $ldapName = $user->name ?? 'Usuario';

        return view('vacaciones.agenda', compact('sam', 'ldapName', 'rol', 'iduser'));
    }

    public function listarAgenda()
    {
        $eventos = FechaVacacione::with('datoUsuario')
            ->orderBy('fecha_inicio')
            ->get()
            ->map(function ($evento) {
                $inicio = Carbon::parse($evento->fecha_inicio);
                $fin = Carbon::parse($evento->fecha_fin)->addDay();

                $nombre = $evento->datoUsuario
                    ? $evento->datoUsuario->nombre . ' ' . $evento->datoUsuario->apaterno
                    : $evento->nombre_usuario;

                $titulo = $evento->asunto ? $evento->asunto . " ({$nombre})" : $nombre;

                return [
                    'id' => $evento->id,
                    'title' => $titulo,
                    'start' => $inicio->format('Y-m-d\TH:i:s'),
                    'end' => $fin->format('Y-m-d\TH:i:s'),
                    'color' => $evento->color ?: $evento->obtenerColorEstatus(),
                    'iduser' => $evento->iduser,
                    'estatus' => $evento->estatus,
                    'allDay' => true,
                    'extendedProps' => [
                        'nombre_usuario' => $nombre,
                        'asunto' => $evento->asunto,
                        'iduser' => $evento->iduser,
                        'estatus' => $evento->estatus,
                    ]
                ];
            });

        return response()->json($eventos);
    }

    // ========== MÉTODOS CRUD BÁSICOS ==========

    public function create()
    {
        // No implementado - se usa registrar()
    }

    public function store(Request $request)
    {
        // No implementado - se usa registrar()
    }

    public function show($id)
    {
        // No implementado
    }

    public function edit($id)
    {
        // No implementado - se maneja en el modal
    }

    public function update(Request $request, $id)
    {
        // No implementado - se usa registrar()
    }

    public function destroy($id)
    {
        // No implementado - se usa eliminarEvento()
    }
}
