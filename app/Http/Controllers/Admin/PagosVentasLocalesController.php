<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagoVentaLocal;
use Illuminate\Http\Request;

class PagosVentasLocalesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = PagoVentaLocal::with('ventaLocal')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pagos_ventas_locales.index', compact('items'));
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = PagoVentaLocal::with('ventaLocal')
            ->findOrFail($id);

        return view('admin.pagos_ventas_locales.show', compact('item'));
    }

    /* ============================================
       ✏️ ACTUALIZAR
    ============================================ */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'metodo' => 'required|in:efectivo,tarjeta,sinpe,mixto',
            'monto' => 'required|numeric|min:0',
            'referencia' => 'nullable|string|max:80',
        ]);

        try {
            $item = PagoVentaLocal::findOrFail($id);

            $item->update([
                'metodo' => $request->metodo,
                'monto' => $request->monto,
                'referencia' => $request->referencia,
            ]);

            return redirect()
                ->route('admin.pagos-ventas-locales.index')
                ->with('success', 'Pago de venta local actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el pago de la venta local.');
        }
    }
}