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

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// use Spatie\Permission\Traits\HasRoles;
use App\Models\Role;
use Spatie\Permission\Traits\HasRoles;




/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $guid
 * @property string|null $domain
 *
 * @package App\Models
 */
class Usuario extends Model
{
	 use HasApiTokens, HasFactory, Notifiable, HasRoles;


	protected $table = 'user';

	protected $guard_name = 'web';
	protected $primaryKey = 'id';



	protected $casts = [
		'email_verified_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token',
		'guid',
		'domain'
	];

	public function guardName()
    {
        return 'web';
    }

    public function getCnAttribute()
    {
        // Suponiendo que la cadena completa está almacenada en el atributo 'cn'
        $fullName = $this->attributes['cn'];

        // Extraer el nombre de la cadena
        if (preg_match('/^CN=([^,]+)/', $fullName, $matches)) {
            return $matches[1];
        }

        return $fullName; // Retornar la cadena completa si no coincide con el patrón
    }

	// public function roles()
	// {
	// 	$userModel = config('auth.providers.users.model');

    // return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
    //     ->withPivot('model_type')
    //     ->wherePivot('model_type', $userModel);
	// }


	 public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->withPivot('model_type')  // Add this line
            ->wherePivot('model_type', 'App\\Models\\User'); // Adjust the namespace to match your Usuario model
    }


}
