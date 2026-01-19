<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Foliocataori
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Foliocataori extends Model
{
	protected $table = 'foliocataori';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
