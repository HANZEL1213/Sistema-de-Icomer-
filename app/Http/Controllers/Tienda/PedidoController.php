<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function misPedidos()
    {
        $usuario = Auth::user();

        $pedidos = Pedido::with([
                'detalle.producto.imagenPrincipal',
                'pagoUltimo',
                'cupon',
                'usoCupon',
            ])
            ->where('id_usuario', $usuario->id_usuario)
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
                'detalle.producto.imagenPrincipal',
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