<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacacionesAcumulada extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'vacaciones_acumuladas';
    
    protected $fillable = [
        'user_id',
        'anio',
        'dias_totales',
        'dias_tomados',
        'dias_pendientes',
        'fecha_corte'
    ];
    
    protected $casts = [
        'anio' => 'integer',
        'dias_totales' => 'integer',
        'dias_tomados' => 'integer',
        'dias_pendientes' => 'integer',
        'fecha_corte' => 'date'
    ];
    
    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relación con datos del usuario
     */
    public function datoUsuario(): BelongsTo
    {
        return $this->belongsTo(DatoUsuario::class, 'user_id', 'user_id');
    }
    
    /**
     * Relación con fechas de vacaciones para este año
     */
    public function fechasVacaciones(): HasMany
    {
        return $this->hasMany(FechaVacacione::class, 'iduser', 'user_id')
            ->whereYear('fecha_inicio', $this->anio)
            ->autorizadas();
    }
    
    /**
     * Calcular automáticamente días tomados
     */
    public function recalcularDiasTomados(): void
    {
        $this->dias_tomados = $this->fechasVacaciones->sum(function ($vacacion) {
            return $vacacion->calcularDiasHabiles();
        });
        
        $this->actualizarPendientes();
    }
    
    /**
     * Actualizar días pendientes
     */
    public function actualizarPendientes(): void
    {
        $this->dias_pendientes = max(0, $this->dias_totales - $this->dias_tomados);
        $this->save();
    }
    
    /**
     * Verificar si se han usado todas las vacaciones
     */
    public function getCompletadoAttribute(): bool
    {
        return $this->dias_pendientes <= 0 && $this->dias_totales > 0;
    }
    
    /**
     * Obtener porcentaje de avance
     */
    public function getPorcentajeAvanceAttribute(): float
    {
        if ($this->dias_totales === 0) {
            return 0;
        }
        
        return ($this->dias_tomados / $this->dias_totales) * 100;
    }
    
    /**
     * Obtener información formateada
     */
    public function getInformacionFormateadaAttribute(): array
    {
        return [
            'anio' => $this->anio,
            'dias_totales' => $this->dias_totales,
            'dias_tomados' => $this->dias_tomados,
            'dias_pendientes' => $this->dias_pendientes,
            'porcentaje_avance' => round($this->porcentaje_avance, 1),
            'completado' => $this->completado,
            'fecha_corte' => $this->fecha_corte ? $this->fecha_corte->format('d/m/Y') : null
        ];
    }
}