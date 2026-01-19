<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Municipio
 * 
 * @property int $idmunicipio
 * @property string $municipio
 * @property int $ciudad_idciudad
 * 
 * @property Ciudad $ciudad
 * @property Collection|Colonium[] $colonia
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Municipio extends Model
{
	protected $table = 'municipio';
	protected $primaryKey = 'idmunicipio';
	public $timestamps = false;

	protected $casts = [
		'ciudad_idciudad' => 'int'
	];

	protected $fillable = [
		'municipio',
		'ciudad_idciudad'
	];

	public function ciudad()
	{
		return $this->belongsTo(Ciudad::class, 'ciudad_idciudad');
	}

	public function colonia()
	{
		return $this->hasMany(Colonium::class, 'municipio_idmunicipio');
	}

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'municipio_idmunicipio');
	}
}
