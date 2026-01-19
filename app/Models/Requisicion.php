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
class Requisicion extends Model
{
    use SoftDeletes;

	protected $table = 'requisicion';
    protected $primaryKey = 'id';
	public $timestamps = true;


	protected $casts = [
		'folio' => 'int',
		'fechasol' => 'datetime',
		'fechares' => 'datetime',
		'estado' => 'int',
		'semanasol' => 'int',
		'semanares' => 'int',
		'empresa' => 'int',
		'costo' => 'int',
		'id_solicitante' => 'int',
		'idtipo_insumo' => 'int'

	];

	protected $fillable = [
		'folio',
		'solicitante',
		'fechasol',
		'fechares',
		'estado',
		'respuesta',
		'semanasol',
		'semanares',
		'empresa',
		'area',
		'costo',
		'id_solicitante',
		'idtipo_insumo',
        'id_userautoriza',
        'id_userpreautoriza',
        'respuestapreautoriza',
        'factura',
        'xml_factura',
        'diagnostico'

	];

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa', 'Id');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'requisicion_id', 'id');
    }


}
