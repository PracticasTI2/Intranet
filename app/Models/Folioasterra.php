<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioasterra
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioasterra extends Model
{
	protected $table = 'folioasterra';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
