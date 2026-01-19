<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Colonium
 *
 * @property int $idcolonia
 * @property string $colonia
 * @property int $municipio_idmunicipio
 * @property string $cp
 *
 * @property Municipio $municipio
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Colonium extends Model
{
	protected $table = 'colonia';
	protected $primaryKey = 'idcolonia';
	public $timestamps = false;

	protected $casts = [
		'municipio_idmunicipio' => 'int'
	];

	protected $fillable = [
		'colonia',
		'municipio_idmunicipio',
		'cp'
	];

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'municipio_idmunicipio');
	}

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'colonia_idcolonia');
	}
}
