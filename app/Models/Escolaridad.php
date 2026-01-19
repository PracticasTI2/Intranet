<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Escolaridad
 *
 * @property int $idescolaridad
 * @property string $escolaridad
 *
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Escolaridad extends Model
{
	protected $table = 'escolaridad';
	protected $primaryKey = 'idescolaridad';
	public $timestamps = false;

	protected $fillable = [
		'escolaridad'
	];

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'escolaridad_idescolaridad');
	}
}
