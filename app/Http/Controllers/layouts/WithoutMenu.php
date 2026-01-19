<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithoutMenu extends Controller
{
  public function index()
  {
    $user = Auth::user();

    return view('content.layouts-example.layouts-without-menu', compact('user'));
  }
}
