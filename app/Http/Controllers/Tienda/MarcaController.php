<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class MarcaController extends Controller
{
    public function index()
    {
        return view('tienda.marcas.index');
    }

    public function show($slug)
    {
        return view('tienda.marcas.show');
    }
}