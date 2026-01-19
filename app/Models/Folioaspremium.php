<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Folioaspremium
 * 
 * @property int $Id
 * @property int $contador
 *
 * @package App\Models
 */
class Folioaspremium extends Model
{
	protected $table = 'folioaspremium';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $casts = [
		'contador' => 'int'
	];

	protected $fillable = [
		'contador'
	];
}
