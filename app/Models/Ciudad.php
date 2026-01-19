<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ciudad
 *
 * @property int $idciudad
 * @property string $ciudad
 * @property int $estado_idestado
 *
 * @property Estado $estado
 * @property Collection|DatoUsuario[] $dato_usuarios
 * @property Collection|Municipio[] $municipios
 *
 * @package App\Models
 */
class Ciudad extends Model
{
	protected $table = 'ciudad';
	protected $primaryKey = 'idciudad';
	public $timestamps = false;

	protected $casts = [
		'estado_idestado' => 'int'
	];

	protected $fillable = [
		'ciudad',
		'estado_idestado'
	];

	public function estado()
	{
		return $this->belongsTo(Estado::class, 'estado_idestado');
	}

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'ciudad_idciudad');
	}

	public function municipios()
	{
		return $this->hasMany(Municipio::class, 'ciudad_idciudad');
	}
}
