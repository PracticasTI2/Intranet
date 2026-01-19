<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;


use Spatie\Permission\Traits\HasRoles;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
// use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class User
 *
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property string $auth_key
 * @property string $password
 * @property string|null $password_reset_token
 * @property int $status
 * @property string|null $guid
 * @property string|null $domain
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property DatoUsuario $dato_usuario
 * @property Collection|DatoUsuario[] $dato_usuarios
 *
 * @package App\Models
 */
class User extends Authenticatable implements LdapAuthenticatable
{
	use SoftDeletes, HasApiTokens, HasFactory, Notifiable, AuthenticatesWithLdap, HasRoles;
	protected $table = 'user';

	protected $primaryKey = 'id';
	public $timestamps = true;

	protected $casts = [
		'status' => 'int'
	];

	protected $hidden = [
		'password',
		'password_reset_token'
	];

	protected $fillable = [
		'name',
		'email',
		'auth_key',
		'password',
		'password_reset_token',
		'status',
		'guid',
		'domain',
		'updated_at',
		'correo'
	];

	public function dato_usuario()
	{
		return $this->hasOne(DatoUsuario::class);
	}

	public function dato_usuarios()
	{
		return $this->hasMany(DatoUsuario::class, 'idjefe');
	}

	public function datoUsuario()
	{
		return $this->hasOne(DatoUsuario::class, 'user_id');
	}

	public function getAuthIdentifierName()
	{
		return 'email';
	}

	public function getLdapDomainColumn(): string
	{
		return 'domain';
	}

	public function getLdapGuidColumn(): string
	{
		return 'guid';
	}



	public function toSearchableArray()
	{


		return [
			'email' => $this->email,
		];
	}

	public function scopeSearch($query, array $params)
	{
		$query->when($params['name'] ?? false, function ($query, $name) {
			$query->where('name', 'like', '%' . $name . '%');
		})
			->when($params['email'] ?? false, function ($query, $email) {
				$query->where('email', 'like', '%' . $email . '%');
			})
			->when($params['fechan'] ?? false, function ($query, $fechan) {
				$query->whereHas('datoUsuario', function ($q) use ($fechan) {
					$q->whereDate('nacimiento', $fechan); // o usar 'like' si es un string parcial
				});
			});
	}


	//  public function scopeSearch($query, array $params)
	// {

	// 	$query->where( function($query) use ($params) {
	// 		$query->when( $params['name'] ?? false, function($query, $name) {

	// 			 $query->where( 'name', 'LIKE', '%'.$name.'%');

	// 		 });

	// 	 })
	// 	 ->when( $params['email'] ?? false, function($query, $email) {

	// 		 $query->whereExists( function($query) use ($email) {
	// 			 $query->where( 'email', 'LIKE', "%{$email}%");
	// 		 });
	// 	 });

	// }

	// aqui lo comento 16 enero ELIMÍNALO. patie ya lo define en HasRoles.
	// public function roles()
	// {
	// 	return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
	// }

	public function vacaciones()
	{
		return $this->hasMany(FechaVacacione::class, 'iduser', 'id');
	}
}
