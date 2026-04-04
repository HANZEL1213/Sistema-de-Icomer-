<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ConfiguracionController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Configuracion::orderBy('clave')->get();
        return view('admin.configuracion.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        return view('admin.configuracion.create');
    }

    public function store(Request $request)
    {
        // 🔥 VALIDACIÓN GLOBAL (usa validation.php)
        $request->validate([
            'clave' => 'required|string|max:80|unique:configuracion,clave',
            'valor' => 'nullable|string',
        ]);

        try {
            Configuracion::create([
                'clave' => $request->clave,
                'valor' => $request->valor,
            ]);

            return redirect()
                ->route('admin.configuracion.index')
                ->with('success', 'Configuración creada correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la configuración.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $clave)
    {
        $item = Configuracion::findOrFail($clave);
        return view('admin.configuracion.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $clave)
    {
        $item = Configuracion::findOrFail($clave);
        return view('admin.configuracion.edit', compact('item'));
    }

    public function update(Request $request, string $clave)
    {
        // 🔥 VALIDACIÓN GLOBAL
        $request->validate([
            'valor' => 'nullable|string',
        ]);

        try {
            $item = Configuracion::findOrFail($clave);

            $item->update([
                'valor' => $request->valor,
            ]);

            return redirect()
                ->route('admin.configuracion.index')
                ->with('success', 'Configuración actualizada correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la configuración.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
public function destroy(string $clave)
{
    try {
        $item = Configuracion::findOrFail($clave);
        $item->delete();

        return redirect()
            ->route('admin.configuracion.index')
            ->with('success', 'Configuración eliminada correctamente.');

    } catch (QueryException $e) {
        return redirect()
            ->route('admin.configuracion.index')
            ->with('error', 'No se pudo eliminar la configuración.');

    } catch (\Exception $e) {
        return redirect()
            ->route('admin.configuracion.index')
            ->with('error', 'Error al eliminar la configuración.');
    }
}
}