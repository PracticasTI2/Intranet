<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except([
    //         'logout', 'dashboard'
    //     ]);
    // }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

   

    protected function credentials(Request $request)
{
    return [
        'samaccountname' => $request->get('username'),
        'password' => $request->get('password')
    ];
}

    public function username()
    {
        return 'username';
    }

    public function register()
    {
        return view('content.authentications.auth-register-basic');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('content.authentications.auth-login-basic');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        // $credentials = $request->validate([
        //     'correo' => 'required|email',
        //     'password' => 'required'
        // ]);

        // if(Auth::attempt($credentials))
        // {
            $request->session()->regenerate();
            return redirect()->route('dashboard-analytics')
                ->withSuccess('You have successfully logged in!');
        // }

        return back()->withErrors([
            'correo' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('correo');

    } 
    
    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::check())
        {
            return view('auth.dashboard');
        }
        
        return redirect()->route('login')
            ->withErrors([
            'correo' => 'Please login to access the dashboard.',
        ])->onlyInput('correo');
    } 
    
    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }    

}