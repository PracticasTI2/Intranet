<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Notum
 *
 * @property int $idnota
 * @property string $titulo
 * @property string $descripcion
 * @property int $tiempo
 * @property int $idtipo_tablero
 * @property string $tipo
 * @property Carbon $publicacion
 * @property Carbon $inicio
 * @property Carbon|null $termino
 * @property string $fijo
 * @property Carbon|null $inicioFijo
 * @property Carbon|null $terminoFijo
 * @property string|null $archivo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property TipoTablero $tipo_tablero
 *
 * @package App\Models
 */
class Nota extends Model
{
	use SoftDeletes;
	protected $table = 'nota';
	protected $primaryKey = 'idnota';

	protected $casts = [
		'tiempo' => 'int',
		'idtipo_tablero' => 'int',
		'publicacion' => 'datetime',
		'inicio' => 'datetime',
		'termino' => 'datetime',
		'inicioFijo' => 'datetime',
		'terminoFijo' => 'datetime'
	];

	protected $fillable = [
		'titulo',
		'descripcion',
		'tiempo',
		'idtipo_tablero',
		'tipo',
		'publicacion',
		'inicio',
		'termino',
		'fijo',
		'inicioFijo',
		'terminoFijo',
		'archivo'
	];

	public function tipo_tablero()
	{
		return $this->belongsTo(TipoTablero::class, 'idtipo_tablero');
	}

     
}
