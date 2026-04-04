<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RolesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Rol::withCount('usuarios')
            ->orderBy('nombre')
            ->get();

        return view('admin.roles.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:150',
            'activo' => 'nullable|boolean',
        ]);

        try {
            Rol::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol creado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el rol.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Rol::withCount('usuarios')->findOrFail($id);
        return view('admin.roles.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Rol::findOrFail($id);
        return view('admin.roles.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre,' . $id . ',id_rol',
            'descripcion' => 'nullable|string|max:150',
            'activo' => 'nullable|boolean',
        ]);

        try {
            $item = Rol::findOrFail($id);

            $item->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el rol.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Rol::findOrFail($id);
            $item->delete();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol eliminado correctamente.');

        } catch (QueryException $e) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'No se pudo eliminar el rol.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Error al eliminar el rol.');
        }
    }
}