<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Foliomegaseminuevo
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Foliomegaseminuevo extends Model
{
	protected $table = 'foliomegaseminuevos';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
