<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class PagoPedidoController extends Controller
{
    public function index($pedido)
    {
        return view('tienda.pedidos.pago');
    }
}