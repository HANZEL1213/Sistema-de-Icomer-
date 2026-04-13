<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagoVentaLocal;

class PagosVentasLocalesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = PagoVentaLocal::with([
                'ventaLocal:id_venta_local,numero_ticket,nombre_cliente,total',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pagos_ventas_locales.index', compact('items'));
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = PagoVentaLocal::with([
                'ventaLocal',
                'ventaLocal.cajero:id_usuario,nombre,correo',
            ])
            ->findOrFail($id);

        return view('admin.pagos_ventas_locales.show', compact('item'));
    }
}