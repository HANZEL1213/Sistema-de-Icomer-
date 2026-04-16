<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class PedidoController extends Controller
{
    public function misPedidos()
    {
        return view('tienda.pedidos.mis_pedidos');
    }

    public function show($codigo)
    {
        return view('tienda.pedidos.show');
    }

    public function seguimiento($codigo)
    {
        return view('tienda.pedidos.seguimiento');
    }
}