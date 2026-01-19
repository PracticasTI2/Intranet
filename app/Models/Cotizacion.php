<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Requisicion
 *
 * @property int $folio
 * @property string $solicitante
 * @property Carbon $fechasol
 * @property Carbon|null $fechares
 * @property int $estado
 * @property string|null $respuesta
 * @property int $semanasol
 * @property int|null $semanares
 * @property int $empresa
 * @property string|null $area
 * @property int|null $costo
 * @property int|null $id_solicitante
 * @property int $id
 *
 * @package App\Models
 */
class Cotizacion extends Model
{
    use SoftDeletes;

	protected $table = 'cotizaciones';
	public $timestamps = true;
    protected $primaryKey = 'id';

	protected $casts = [

		'requisicion_id' => 'int',
		'archivo' => 'string',


	];

	protected $fillable = [
		'requisicion_id',
		'archivo',
        'estatus'


	];

    public function requisicion()
    {
        return $this->belongsTo(Requisicion::class, 'requisicion_id', 'id');
    }


}
