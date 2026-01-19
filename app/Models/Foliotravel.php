<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Foliotravel
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Foliotravel extends Model
{
	protected $table = 'foliotravel';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
