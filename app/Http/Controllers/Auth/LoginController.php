<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;


use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use LdapRecord\Laravel\Auth\ListensForLdapBindFailure;

use App\Models\User;

use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use LdapRecord\Laravel\Auth\FailedLdapLogin;
// use LdapRecord\Laravel\Auth\LdapAuthenticatable;
// use LdapRecord\Laravel\Auth\HandlesLdapBindExceptions;

class LoginController extends Controller
{
      use AuthenticatesUsers;

     protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Instantiate a new LoginRegisterController instance.
     */

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {

          $credentials = [
        'samaccountname' => $request->get('username'),
        'password' => $request->get('password'),
    ];

    // Verifica si el usuario es uno de los permitidos
    // $allowedUsers = ['mlopez', 'tramirez','rsanchezfa','rhernandez','sergio','exicali','caguilar','erosado','ehernandez','lvillegas'];
    // if (!in_array($credentials['samaccountname'], $allowedUsers)) {
    //     // Usuario no permitido
    //     return null;
    // }

    return $credentials;
    }


public function login(Request $request)
{
    $credentials = $this->credentials($request);

    if (empty($credentials) || !Auth::guard('web')->attempt($credentials)) {
        // Autenticación fallida
        return redirect()->back()->withErrors(['message' => 'Credenciales incorrectas']);
    }

    // Autenticación exitosa
    // return redirect()->intended('/content/dashboard/dashboards-analytics');   $user = Auth::user();
    //    $user = Auth::user();
    //   $role = Role::firstOrCreate(['name' => 'usuario']);
    //     $user->assignRole($role);

    return redirect()->route('dashboard-analytics');
}




}
