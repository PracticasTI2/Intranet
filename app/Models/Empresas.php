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
class Empresas extends Model
{
	protected $table = 'empresas';
	protected $primaryKey = 'Id';
	public $timestamps = false;

	protected $fillable = [
		'rfc',
		'direccion',
		'telefono',
		'fax',
		'logo'
	];
}
