<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioantena
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioantena extends Model
{
	protected $table = 'folioantena';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
