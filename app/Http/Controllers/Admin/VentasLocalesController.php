<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VentaLocal;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class VentasLocalesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = VentaLocal::with(['cajero', 'pagos', 'venta'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ventas_fisicas.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('admin.ventas_fisicas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_ticket' => 'required|string|max:30|unique:ventas_locales,numero_ticket',
            'id_usuario_cajero' => 'required|exists:usuarios,id_usuario',
            'nombre_cliente' => 'nullable|string|max:120',
            'telefono_cliente' => 'nullable|string|max:30',
            'subtotal' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:255',
        ]);

        try {
            VentaLocal::create([
                'numero_ticket' => $request->numero_ticket,
                'id_usuario_cajero' => $request->id_usuario_cajero,
                'nombre_cliente' => $request->nombre_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'subtotal' => $request->subtotal,
                'descuento' => $request->descuento ?? 0,
                'total' => $request->total,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('admin.ventas-locales.index')
                ->with('success', 'Venta local creada correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la venta local.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = VentaLocal::with([
            'cajero',
            'detalle',
            'pagos',
            'venta',
        ])->findOrFail($id);

        return view('admin.ventas_fisicas.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = VentaLocal::findOrFail($id);
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('admin.ventas_fisicas.edit', compact('item', 'usuarios'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'numero_ticket' => 'required|string|max:30|unique:ventas_locales,numero_ticket,' . $id . ',id_venta_local',
            'id_usuario_cajero' => 'required|exists:usuarios,id_usuario',
            'nombre_cliente' => 'nullable|string|max:120',
            'telefono_cliente' => 'nullable|string|max:30',
            'subtotal' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:255',
        ]);

        try {
            $item = VentaLocal::findOrFail($id);

            $item->update([
                'numero_ticket' => $request->numero_ticket,
                'id_usuario_cajero' => $request->id_usuario_cajero,
                'nombre_cliente' => $request->nombre_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'subtotal' => $request->subtotal,
                'descuento' => $request->descuento ?? 0,
                'total' => $request->total,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('admin.ventas-locales.index')
                ->with('success', 'Venta local actualizada correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la venta local.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = VentaLocal::findOrFail($id);
            $item->delete();

            return redirect()
                ->route('admin.ventas-locales.index')
                ->with('success', 'Venta local eliminada correctamente.');

        } catch (QueryException $e) {
            return redirect()
                ->route('admin.ventas-locales.index')
                ->with('error', 'No se pudo eliminar la venta local.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ventas-locales.index')
                ->with('error', 'Error al eliminar la venta local.');
        }
    }
}