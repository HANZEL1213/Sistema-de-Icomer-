<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoriasController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Categoria::orderBy('nombre')->get();

        return view('admin.categorias.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
   public function create()
{
    return view('admin.categorias.create');
}

public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:120|unique:categorias,nombre',
        'slug' => 'required|string|max:160|unique:categorias,slug',
        'descripcion' => 'nullable|string',
        'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'activo' => 'nullable|boolean',
    ]);

    try {
        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('categorias', 'public');
        }

        Categoria::create([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->slug),
            'descripcion' => $request->descripcion,
            'imagen' => $rutaImagen,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error al crear la categoría.');
    }
}

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = Categoria::findOrFail($id);

        return view('admin.categorias.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        $item = Categoria::findOrFail($id);

        return view('admin.categorias.edit', compact('item'));
    }

 public function update(Request $request, string $id)
{
    $request->validate([
        'nombre' => 'required|string|max:120|unique:categorias,nombre,' . $id . ',id_categoria',
        'slug' => 'required|string|max:160|unique:categorias,slug,' . $id . ',id_categoria',
        'descripcion' => 'nullable|string',
        'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'activo' => 'nullable|boolean',
        'eliminar_imagen' => 'nullable|in:0,1',
    ]);

    try {
        $item = Categoria::findOrFail($id);

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

            $rutaImagen = $request->file('imagen')->store('categorias', 'public');
        }

        $item->update([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->slug),
            'descripcion' => $request->descripcion,
            'imagen' => $rutaImagen,
            'activo' => $request->has('activo') ? 1 : 0,
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error al actualizar la categoría.');
    }
}
    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        try {
            $item = Categoria::findOrFail($id);

            if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                Storage::disk('public')->delete($item->imagen);
            }

            $item->delete();

            return redirect()
                ->route('admin.categorias.index')
                ->with('success', 'Categoría eliminada correctamente.');

        } catch (QueryException $e) {
            return redirect()
                ->route('admin.categorias.index')
                ->with('error', 'No se pudo eliminar la categoría.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.categorias.index')
                ->with('error', 'Error al eliminar la categoría.');
        }
    }
}