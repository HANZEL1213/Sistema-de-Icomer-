<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class PedidoController extends Controller
{
    public function misPedidos()
    {
        $pedidos = Pedido::with([
                'detalle',
                'pagoUltimo',
                'cupon',
                'usoCupon',
            ])
            ->latest('created_at')
            ->get();

        return view('tienda.pedidos.mis_pedidos', compact('pedidos'));
    }

    public function show($codigo)
    {
        $pedido = Pedido::with([
                'detalle.producto.imagenPrincipal',
                'pagoUltimo',
                'cupon',
                'usoCupon',
            ])
            ->where('numero_pedido', $codigo)
            ->orWhere('codigo_seguimiento_publico', $codigo)
            ->firstOrFail();

        return view('tienda.pedidos.show', compact('pedido'));
    }

    public function seguimiento($codigo)
    {
        $pedido = Pedido::with([
                'detalle',
                'pagoUltimo',
                'cupon',
                'usoCupon',
            ])
            ->where('numero_pedido', $codigo)
            ->orWhere('codigo_seguimiento_publico', $codigo)
            ->firstOrFail();

        return view('tienda.pedidos.seguimiento', compact('pedido'));
    }
}