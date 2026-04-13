<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagoPedido;
use Illuminate\Http\Request;

class PagosPedidosController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */


    public function index()
{
    $items = PagoPedido::with([
        'pedido:id_pedido,numero_pedido,nombre_cliente',
        'verificador:id_usuario,nombre'
    ])
    ->orderByDesc('enviado_en')
    ->get();

    return view('admin.pagos_pedidos.index', compact('items'));
}

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = PagoPedido::with(['pedido', 'verificador'])
            ->findOrFail($id);

        return view('admin.pagos_pedidos.show', compact('item'));
    }


}