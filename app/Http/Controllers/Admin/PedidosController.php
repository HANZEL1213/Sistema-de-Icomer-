<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Cupon;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Pedido::with(['usuario', 'cupon', 'pagoUltimo', 'venta'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pedidos.index', compact('items'));
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Pedido::with([
            'usuario',
            'cupon',
            'detalle',
            'pagos',
            'pagoUltimo',
            'venta',
        ])->findOrFail($id);

        return view('admin.pedidos.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Pedido::findOrFail($id);

        $usuarios = Usuario::orderBy('nombre')->get();
        $cupones = Cupon::orderBy('codigo')->get();

        return view('admin.pedidos.edit', compact('item', 'usuarios', 'cupones'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente_pago,en_revision,pagado_verificado,preparando,enviado,entregado,rechazado,cancelado',
            'id_usuario' => 'nullable|exists:usuarios,id_usuario',

            'nombre_cliente' => 'required|string|max:120',
            'telefono_cliente' => 'required|string|max:30',
            'correo_cliente' => 'nullable|email|max:190',

            'tipo_entrega' => 'required|in:retiro,envio',
            'provincia_envio' => 'nullable|string|max:80',
            'canton_envio' => 'nullable|string|max:80',
            'distrito_envio' => 'nullable|string|max:80',
            'direccion_envio' => 'nullable|string|max:255',
            'referencia_envio' => 'nullable|string|max:255',

            'costo_envio' => 'required|numeric|min:0',

            'id_cupon' => 'nullable|exists:cupones,id_cupon',
            'codigo_cupon' => 'nullable|string|max:60',
            'descuento' => 'required|numeric|min:0',

            'subtotal' => 'required|numeric|min:0',
            'subtotal_con_descuento' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',

            'notas' => 'nullable|string|max:255',
            'codigo_seguimiento_publico' => 'nullable|string|max:64|unique:pedidos,codigo_seguimiento_publico,' . $id . ',id_pedido',
        ]);

        try {
            $item = Pedido::findOrFail($id);

            $item->update([
                'estado' => $request->estado,
                'id_usuario' => $request->id_usuario,

                'nombre_cliente' => $request->nombre_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'correo_cliente' => $request->correo_cliente,

                'tipo_entrega' => $request->tipo_entrega,
                'provincia_envio' => $request->provincia_envio,
                'canton_envio' => $request->canton_envio,
                'distrito_envio' => $request->distrito_envio,
                'direccion_envio' => $request->direccion_envio,
                'referencia_envio' => $request->referencia_envio,

                'costo_envio' => $request->costo_envio,

                'id_cupon' => $request->id_cupon,
                'codigo_cupon' => $request->codigo_cupon,
                'descuento' => $request->descuento,

                'subtotal' => $request->subtotal,
                'subtotal_con_descuento' => $request->subtotal_con_descuento,
                'total' => $request->total,

                'notas' => $request->notas,
                'codigo_seguimiento_publico' => $request->codigo_seguimiento_publico,
            ]);

            return redirect()
                ->route('admin.pedidos.index')
                ->with('success', 'Pedido actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el pedido.');
        }
    }
}