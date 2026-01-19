<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DatoUsuario extends Model
{
	use SoftDeletes;

	protected $table = 'dato_usuario';
	protected $primaryKey = 'user_id';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'user_id' => 'int',
		'ingreso' => 'datetime',
		'nacimiento' => 'datetime',
		'egreso' => 'datetime',
		'puesto_idpuesto' => 'int',
		'empresa_idempresa' => 'int',
		'area_idarea' => 'int',
		'visible' => 'int',
		'colonia_idcolonia' => 'int',
		'idestado_usuario' => 'int',
		'idjefe' => 'int',
		'estado_idestado' => 'int',
		'ciudad_idciudad' => 'int',
		'municipio_idmunicipio' => 'int',
		'escolaridad_idescolaridad' => 'int'
	];

	protected $fillable = [
		'user_id',
		'nombre',
		'apaterno',
		'amaterno',
		'ingreso',
		'nacimiento',
		'egreso',
		'puesto_idpuesto',
		'empresa_idempresa',
		'foto',
		'area_idarea',
		'visible',
		'review',
		'colonia_idcolonia',
		'idestado_usuario',
		'idjefe',
		'estado_idestado',
		'ciudad_idciudad',
		'municipio_idmunicipio',
		'escolaridad_idescolaridad',
		'correo'
	];

	public function area()
	{
		return $this->belongsTo(Area::class, 'area_idarea');
	}

	public function ciudad()
	{
		return $this->belongsTo(Ciudad::class, 'ciudad_idciudad');
	}

	public function colonium()
	{
		return $this->belongsTo(Colonium::class, 'colonia_idcolonia');
	}

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'empresa_idempresa');
	}

	public function escolaridad()
	{
		return $this->belongsTo(Escolaridad::class, 'escolaridad_idescolaridad');
	}

	public function estado()
	{
		return $this->belongsTo(Estado::class, 'estado_idestado');
	}

	public function estado_usuario()
	{
		return $this->belongsTo(EstadoUsuario::class, 'idestado_usuario');
	}

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'municipio_idmunicipio');
	}

	public function puesto()
	{
		return $this->belongsTo(Puesto::class, 'puesto_idpuesto');
	}

	// public function user()
	// {
	// 	return $this->belongsTo(User::class, 'idjefe');
	// }

	public function correos()
	{
		return $this->hasMany(Correo::class, 'user_id');
	}

	// Nuevo

	public function usuario()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function jefe()
	{
		return $this->belongsTo(User::class, 'idjefe', 'id');
	}
}
