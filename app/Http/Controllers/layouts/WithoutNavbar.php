<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithoutNavbar extends Controller
{
  public function index()
  {
   
     $user = auth()->user();
    $ldapName = $user->name; 

    return view('content.layouts-example.layouts-without-navbar', compact('ldapName'));
  }
}
