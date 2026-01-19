<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Unidad
 * 
 * @property string|null $clave
 * @property string|null $descripcion
 *
 * @package App\Models
 */
class Unidad extends Model
{
	protected $table = 'unidad';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'clave',
		'descripcion'
	];
}
