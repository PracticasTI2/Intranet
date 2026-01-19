<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioorientel
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioorientel extends Model
{
	protected $table = 'folioorientel';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
