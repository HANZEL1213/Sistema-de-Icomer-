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
        $items = PagoPedido::with(['pedido', 'verificador'])
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

    /* ============================================
       ✏️ REVISAR / ACTUALIZAR ESTADO
    ============================================ */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'estado' => 'required|in:enviado,verificado,rechazado',
            'motivo_rechazo' => 'nullable|string|max:255',
            'id_usuario_verificador' => 'nullable|exists:usuarios,id_usuario',
        ]);

        try {
            $item = PagoPedido::findOrFail($id);

            $data = [
                'estado' => $request->estado,
                'motivo_rechazo' => null,
                'id_usuario_verificador' => null,
                'verificado_en' => null,
            ];

            if ($request->estado === 'verificado') {
                $data['id_usuario_verificador'] = $request->id_usuario_verificador;
                $data['verificado_en'] = now();
            }

            if ($request->estado === 'rechazado') {
                $data['id_usuario_verificador'] = $request->id_usuario_verificador;
                $data['verificado_en'] = now();
                $data['motivo_rechazo'] = $request->motivo_rechazo;
            }

            $item->update($data);

            return redirect()
                ->route('admin.pagos_pedidos.index')
                ->with('success', 'Pago actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el pago.');
        }
    }
}