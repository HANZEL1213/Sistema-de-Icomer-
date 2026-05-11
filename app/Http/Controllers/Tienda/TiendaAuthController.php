<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TiendaAuthController extends Controller
{
    
    public function showLogin()
    {
        return view('tienda.login.index');
    }
}
