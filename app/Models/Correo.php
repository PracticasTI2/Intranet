<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Correo
 *
 * @property int $idcorreo
 * @property string $correo
 * @property int $user_id
 * @property int $idtipo_contacto
 *
 * @property DatoUsuario $dato_usuario
 * @property TipoContacto $tipo_contacto
 *
 * @package App\Models
 */
class Correo extends Model
{
	protected $table = 'correo';
	protected $primaryKey = 'idcorreo';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'idtipo_contacto' => 'int'
	];

	protected $fillable = [
		'correo',
		'user_id',
		'idtipo_contacto'
	];

	public function dato_usuario()
	{
		return $this->belongsTo(DatoUsuario::class, 'user_id');
	}

	public function tipo_contacto()
	{
		return $this->belongsTo(TipoContacto::class, 'idtipo_contacto');
	}
}
