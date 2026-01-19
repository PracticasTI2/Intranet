<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Requisicion;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
//use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
//use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Arr;
use App\Models\User;

class Analytics extends Controller
{
  public function index()
  {
  $usuarioLogueado = Auth::user();
        $usuarioLogueadoId = $usuarioLogueado->id;
        $userRole = $usuarioLogueado->roles->pluck('name')->all();

      $requisiciones= Requisicion::get();


  // $userRole = $user->roles();


    return view('content.dashboard.dashboards-analytics', compact('requisiciones','userRole','usuarioLogueadoId'));
  }



}
