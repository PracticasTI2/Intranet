<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Unidad
 *
 * @property string|null $clave
 * @property string|null $descripcion
 *
 * @package App\Models
 */
class TipoInsumo extends Model
{

      use SoftDeletes;

	protected $table = 'tipo_insumo';
	public $incrementing = true;
	public $timestamps = true;

	protected $fillable = [
		'nombre_insumo',

	];
}
