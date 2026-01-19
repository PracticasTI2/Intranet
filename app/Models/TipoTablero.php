<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TipoTablero
 * 
 * @property int $idtipo_tablero
 * @property int $tiempo
 * @property string $nombre
 * @property string $porcentaje
 * 
 * @property Collection|Notum[] $nota
 *
 * @package App\Models
 */
class TipoTablero extends Model
{
	protected $table = 'tipo_tablero';
	protected $primaryKey = 'idtipo_tablero';
	public $timestamps = false;

	protected $casts = [
		'tiempo' => 'int'
	];

	protected $fillable = [
		'tiempo',
		'nombre',
		'porcentaje'
	];

	public function nota()
	{
		return $this->hasMany(Notum::class, 'idtipo_tablero');
	}
}
