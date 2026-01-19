<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 *
 * @property int $idarea
 * @property string $nombre
 *
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class Area extends Model
{
	protected $table = 'area';
	protected $primaryKey = 'idarea';
	public $timestamps = true;

	protected $fillable = [
		'nombre',
        'id_encargado'
	];

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'area_idarea');
	}

      public function scopeSearch($query, array $params)
	{

		$query->where( function($query) use ($params) {
			$query->when( $params['nombre'] ?? false, function($query, $name) {

				 $query->where( 'nombre', 'LIKE', '%'.$name.'%');

			 });

		 
		 });

	}
}
