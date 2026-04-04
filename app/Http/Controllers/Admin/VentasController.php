<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;

class VentasController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Venta::with([
                'pedido.usuario',
                'ventaLocal.cajero',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ventas.index', compact('items'));
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Venta::with([
                'pedido.usuario',
                'pedido.cupon',
                'pedido.pagoUltimo',
                'ventaLocal.cajero',
                'ventaLocal.pagos',
            ])
            ->findOrFail($id);

        return view('admin.ventas.show', compact('item'));
    }
}