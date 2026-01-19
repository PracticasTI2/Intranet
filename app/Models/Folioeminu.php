<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioeminu
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioeminu extends Model
{
	protected $table = 'folioeminus';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
