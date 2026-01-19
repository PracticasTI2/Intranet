<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Empresa
 *
 * @property int $Id
 * @property string $rfc
 * @property string $direccion
 * @property string $telefono
 * @property string $fax
 * @property string $logo
 *
 * @package App\Models
 */
class Empresa extends Model
{
	protected $table = 'empresa';
	protected $primaryKey = 'idempresa';
	public $timestamps = false;

	protected $fillable = [
		'empresa'

	];
}
