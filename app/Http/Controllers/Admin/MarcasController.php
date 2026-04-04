<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarcasController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Marca::orderBy('nombre')->get();

        return view('admin.marcas.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        return view('admin.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120|unique:marcas,nombre',
            'slug' => 'required|string|max:160|unique:marcas,slug',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'activo' => 'nullable|boolean',
        ]);

        try {
            $rutaImagen = null;

            if ($request->hasFile('imagen')) {
                $rutaImagen = $request->file('imagen')->store('marcas', 'public');
            }

            Marca::create([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->slug),
                'imagen' => $rutaImagen,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.marcas.index')
                ->with('success', 'Marca creada correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la marca.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Marca::findOrFail($id);

        return view('admin.marcas.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Marca::findOrFail($id);

        return view('admin.marcas.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:120|unique:marcas,nombre,' . $id . ',id_marca',
            'slug' => 'required|string|max:160|unique:marcas,slug,' . $id . ',id_marca',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'activo' => 'nullable|boolean',
            'eliminar_imagen' => 'nullable|in:0,1',
        ]);

        try {
            $item = Marca::findOrFail($id);

            $rutaImagen = $item->imagen;

            if ($request->eliminar_imagen == '1') {
                if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                    Storage::disk('public')->delete($item->imagen);
                }

                $rutaImagen = null;
            }

            if ($request->hasFile('imagen')) {
                if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                    Storage::disk('public')->delete($item->imagen);
                }

                $rutaImagen = $request->file('imagen')->store('marcas', 'public');
            }

            $item->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->slug),
                'imagen' => $rutaImagen,
                'activo' => $request->has('activo') ? 1 : 0,
            ]);

            return redirect()
                ->route('admin.marcas.index')
                ->with('success', 'Marca actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la marca.');
        }
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Marca::findOrFail($id);

            if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                Storage::disk('public')->delete($item->imagen);
            }

            $item->delete();

            return redirect()
                ->route('admin.marcas.index')
                ->with('success', 'Marca eliminada correctamente.');
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.marcas.index')
                ->with('error', 'No se pudo eliminar la marca.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.marcas.index')
                ->with('error', 'Error al eliminar la marca.');
        }
    }
}