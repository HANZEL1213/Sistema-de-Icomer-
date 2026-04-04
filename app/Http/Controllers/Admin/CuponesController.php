<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CuponesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Cupon::orderByDesc('created_at')->get();

        return view('admin.cupones.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        return view('admin.cupones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:60|unique:cupones,codigo',
            'tipo' => 'required|in:porcentaje,monto_fijo',
            'valor' => 'required|numeric|min:0',
            'minimo_subtotal' => 'nullable|numeric|min:0',
            'inicia_en' => 'nullable|date',
            'termina_en' => 'nullable|date|after_or_equal:inicia_en',
            'max_usos_total' => 'nullable|integer|min:1',
            'max_usos_por_usuario' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        try {
            Cupon::create([
                'codigo' => strtoupper(trim($request->codigo)),
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'minimo_subtotal' => $request->minimo_subtotal ?? 0,
                'inicia_en' => $request->inicia_en,
                'termina_en' => $request->termina_en,
                'max_usos_total' => $request->max_usos_total,
                'max_usos_por_usuario' => $request->max_usos_por_usuario,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón creado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el cupón.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Cupon::findOrFail($id);

        return view('admin.cupones.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Cupon::findOrFail($id);

        return view('admin.cupones.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:60|unique:cupones,codigo,' . $id . ',id_cupon',
            'tipo' => 'required|in:porcentaje,monto_fijo',
            'valor' => 'required|numeric|min:0',
            'minimo_subtotal' => 'nullable|numeric|min:0',
            'inicia_en' => 'nullable|date',
            'termina_en' => 'nullable|date|after_or_equal:inicia_en',
            'max_usos_total' => 'nullable|integer|min:1',
            'max_usos_por_usuario' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        try {
            $item = Cupon::findOrFail($id);

            $item->update([
                'codigo' => strtoupper(trim($request->codigo)),
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'minimo_subtotal' => $request->minimo_subtotal ?? 0,
                'inicia_en' => $request->inicia_en,
                'termina_en' => $request->termina_en,
                'max_usos_total' => $request->max_usos_total,
                'max_usos_por_usuario' => $request->max_usos_por_usuario,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cupón.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Cupon::findOrFail($id);
            $item->delete();

            return redirect()
                ->route('admin.cupones.index')
                ->with('success', 'Cupón eliminado correctamente.');

        } catch (QueryException $e) {
            return redirect()
                ->route('admin.cupones.index')
                ->with('error', 'No se pudo eliminar el cupón.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.cupones.index')
                ->with('error', 'Error al eliminar el cupón.');
        }
    }
}