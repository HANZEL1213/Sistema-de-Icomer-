<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('tienda.home.index');
    }
}