<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class CarritoController extends Controller
{
    public function index()
    {
        return view('tienda.carrito.index');
    }
}