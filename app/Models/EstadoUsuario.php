<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EstadoUsuario
 *
 * @property int $idestado_usuario
 * @property string $estado
 *
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class EstadoUsuario extends Model
{
	protected $table = 'estado_usuario';
	protected $primaryKey = 'idestado_usuario';
	public $timestamps = false;

	protected $fillable = [
		'estado'
	];

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'idestado_usuario');
	}
}
