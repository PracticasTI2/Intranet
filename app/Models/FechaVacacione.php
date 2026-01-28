<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FechaVacacione
 *
 * @property int $id
 * @property string|null $nombre_usuario
 * @property Carbon|null $fecha_inicio
 * @property Carbon|null $fecha_fin
 * @property int $id_usuario
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class FechaVacacione extends Model
{
    use SoftDeletes;

    protected $table = 'fecha_vacaciones';

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'iduser'       => 'int',
        'estatus'      => 'int',
    ];

    protected $fillable = [
        'nombre_usuario',
        'asunto',
        'fecha_inicio',
        'fecha_fin',
        'iduser',
        'color',
        'estatus',
    ];

    // Relación: el dueño de las vacaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    // (Opcional) acceso directo al perfil DatoUsuario
    public function datoUsuario()
    {
        return $this->belongsTo(DatoUsuario::class, 'iduser', 'user_id');
    }
    /**
     * Calcular días hábiles de esta vacación (excluyendo fines de semana)
     */
    public function calcularDiasHabiles(): int
    {
        $inicio = Carbon::parse($this->fecha_inicio);
        $fin = Carbon::parse($this->fecha_fin);

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
     * Relación con vacaciones acumuladas para el año de esta vacación
     */
    public function vacacionAcumulada()
    {
        $anioVacacion = Carbon::parse($this->fecha_inicio)->year;

        return $this->belongsTo(VacacionesAcumulada::class, 'iduser', 'user_id')
            ->where('anio', $anioVacacion);
    }

    /**
     * Obtener el año de la vacación
     */
    public function obtenerAnio(): int
    {
        return Carbon::parse($this->fecha_inicio)->year;
    }

    /**
     * Verificar si la vacación está autorizada
     */
    public function estaAutorizada(): bool
    {
        return $this->estatus === 1;
    }

    /**
     * Verificar si la vacación está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estatus === 3;
    }

    /**
     * Verificar si la vacación fue rechazada
     */
    public function estaRechazada(): bool
    {
        return $this->estatus === 2;
    }

    /**
     * Obtener el nombre del estatus
     */
    public function obtenerEstatusTexto(): string
    {
        return match ($this->estatus) {
            1 => 'Autorizado',
            2 => 'Rechazado',
            3 => 'Pendiente',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el color según el estatus
     */
    public function obtenerColorEstatus(): string
    {
        return match ($this->estatus) {
            1 => $this->color ?: '#276621', // Verde si está autorizado
            2 => '#FF0000', // Rojo si está rechazado
            3 => '#808080', // Gris si está pendiente
            default => '#999999'
        };
    }

    /**
     * Verificar si la vacación está en el año actual
     */
    public function esDelAnioActual(): bool
    {
        return $this->obtenerAnio() === date('Y');
    }

    /**
     * Scope para obtener solo vacaciones autorizadas
     */
    public function scopeAutorizadas($query)
    {
        return $query->where('estatus', 1);
    }

    /**
     * Scope para obtener vacaciones pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estatus', 3);
    }

    /**
     * Scope para obtener vacaciones de un año específico
     */
    public function scopeDelAnio($query, $anio)
    {
        return $query->whereYear('fecha_inicio', $anio);
    }

    /**
     * Scope para obtener vacaciones de un usuario específico
     */
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('iduser', $userId);
    }

    /**
     * Eventos del modelo para actualizar acumulados
     */
    protected static function boot()
    {
        parent::boot();

        // Cuando se crea o actualiza una vacación autorizada
        static::saved(function ($vacacion) {
            if ($vacacion->estaAutorizada()) {
                $vacacion->actualizarAcumulados();
            }
        });

        // Cuando se elimina una vacación autorizada
        static::deleted(function ($vacacion) {
            if ($vacacion->estaAutorizada()) {
                $vacacion->actualizarAcumulados();
            }
        });
    }

    /**
     * Actualizar la tabla de vacaciones acumuladas
     */
    public function actualizarAcumulados(): void
    {
        if (!$this->estaAutorizada()) {
            return;
        }

        $userId = $this->iduser;
        $anio = $this->obtenerAnio();

        // Obtener o crear registro acumulado
        $acumulada = VacacionesAcumulada::firstOrCreate(
            [
                'user_id' => $userId,
                'anio' => $anio
            ],
            [
                'dias_totales' => 0,
                'dias_tomados' => 0,
                'dias_pendientes' => 0
            ]
        );

        // Si no tenemos días totales, calcularlos según LFT
        if ($acumulada->dias_totales === 0) {
            $datoUsuario = DatoUsuario::where('user_id', $userId)->first();
            if ($datoUsuario && $datoUsuario->ingreso) {
                $acumulada->dias_totales = $this->calcularDiasLFT($datoUsuario->ingreso, $anio);
            }
        }

        // Recalcular días tomados para este año
        $diasTomadosAnio = FechaVacacione::where('iduser', $userId)
            ->autorizadas()
            ->delAnio($anio)
            ->get()
            ->sum(function ($vacacion) {
                return $vacacion->calcularDiasHabiles();
            });

        $acumulada->dias_tomados = $diasTomadosAnio;
        $acumulada->dias_pendientes = max(0, $acumulada->dias_totales - $acumulada->dias_tomados);
        $acumulada->fecha_corte = Carbon::now();
        $acumulada->save();
    }

    /**
     * Calcular días según Ley Federal del Trabajo (Artículo 76)
     * (Este método debería estar en un Service, pero lo ponemos aquí por simplicidad)
     */
    // private function calcularDiasLFT($fechaIngreso, $anioCalcular): int
    // {
    //     $ingreso = Carbon::parse($fechaIngreso);
    //     $finAnio = Carbon::create($anioCalcular, 12, 31);

    //     // Calcular años completos trabajados hasta fin del año
    //     $aniosCompletos = $ingreso->diffInYears($finAnio, false);

    //     if ($aniosCompletos < 0) {
    //         return 0; // Aún no había ingresado
    //     }

    //     // Tabla LFT
    //     return match ($aniosCompletos) {
    //         // 0 => 12, // Menos de 1 año pero más de 6 meses
    //         1 => 12,
    //         2 => 14,
    //         3 => 16,
    //         4 => 18,
    //         5 => 20,
    //         6, 7, 8, 9 => 22,
    //         10, 11, 12, 13, 14 => 24,
    //         15, 16, 17, 18, 19 => 26,
    //         20 => 28,
    //         default => 28 + floor(($aniosCompletos - 20) / 5) * 2 // +2 días cada 5 años después de 20
    //     };
    // }

    /**
     * Formatear fechas para mostrar
     */
    public function getFechasFormateadasAttribute(): array
    {
        return [
            'inicio' => Carbon::parse($this->fecha_inicio)->format('d/m/Y'),
            'fin' => Carbon::parse($this->fecha_fin)->format('d/m/Y'),
            'duracion' => $this->calcularDiasHabiles() . ' días hábiles'
        ];
    }

    /**
     * Obtener información completa de la vacación
     */
    public function getInformacionCompletaAttribute(): array
    {
        return [
            'id' => $this->id,
            'usuario' => $this->nombre_usuario,
            'asunto' => $this->asunto,
            'fechas' => $this->fechas_formateadas,
            'estatus' => [
                'id' => $this->estatus,
                'texto' => $this->obtenerEstatusTexto(),
                'color' => $this->obtenerColorEstatus()
            ],
            'dias_habiles' => $this->calcularDiasHabiles(),
            'anio' => $this->obtenerAnio(),
            'es_actual' => $this->esDelAnioActual(),
            'creado' => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i') : null
        ];
    }
}
