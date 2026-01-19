<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TipoContacto
 * 
 * @property int $idtipo_contacto
 * @property string $tipo
 * 
 * @property Collection|Correo[] $correos
 *
 * @package App\Models
 */
class TipoContacto extends Model
{
	protected $table = 'tipo_contacto';
	protected $primaryKey = 'idtipo_contacto';
	public $timestamps = false;

	protected $fillable = [
		'tipo'
	];

	public function correos()
	{
		return $this->hasMany(Correo::class, 'idtipo_contacto');
	}
}
