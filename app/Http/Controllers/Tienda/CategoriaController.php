<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('tienda.categorias.index');
    }

    public function show($slug)
    {
        return view('tienda.categorias.show');
    }
}