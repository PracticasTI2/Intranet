<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioalsen
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioalsen extends Model
{
	protected $table = 'folioalsen';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
