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
}

