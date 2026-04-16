<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class ProductoController extends Controller
{
    public function index()
    {
        return view('tienda.productos.index');
    }

    public function show($slug)
    {
        return view('tienda.productos.show');
    }
}