<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Puesto
 *
 * @property int $idpuesto
 * @property string $puesto
 *
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Puesto extends Model
{
	protected $table = 'puesto';
	protected $primaryKey = 'idpuesto';
	public $timestamps = false;

	protected $fillable = [
		'puesto'
	];

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'puesto_idpuesto');
	}
}
