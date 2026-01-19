<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estado
 *
 * @property int $idestado
 * @property string $estado
 *
 * @property Collection|Ciudad[] $ciudads
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Estado extends Model
{
	protected $table = 'estado';
	protected $primaryKey = 'idestado';
	public $timestamps = false;

	protected $fillable = [
		'estado'
	];

	public function ciudads()
	{
		return $this->hasMany(Ciudad::class, 'estado_idestado');
	}

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'estado_idestado');
	}
}
