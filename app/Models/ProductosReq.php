<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductosReq
 *
 * @property int $id
 * @property int $folio
 * @property int $empresa
 * @property int $cantidad
 * @property string $unidad
 * @property string $descripcion
 * @property string $justificacion
 * @property string|null $proveedor
 * @property float|null $costo_unidad
 * @property int $incluir
 *
 * @package App\Models
 */
class ProductosReq extends Model
{
      use SoftDeletes;

	protected $table = 'productos_req';
	public $timestamps = true;

	protected $casts = [
		'folio' => 'int',
		'empresa' => 'int',
		'cantidad' => 'int',
		'costo_unidad' => 'float',
		'incluir' => 'int'
	];

	protected $fillable = [
		'folio',
		'empresa',
		'cantidad',
		'unidad',
		'descripcion',
		'justificacion',
		'proveedor',
		'costo_unidad',
		'incluir'
	];
}
