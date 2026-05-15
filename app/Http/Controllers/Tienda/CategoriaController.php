<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Favorito;          // ← Agregado

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::query()
            ->where('activo', 1)

            // Buscar categoría por nombre
            ->when($request->filled('q'), function ($query) use ($request) {

                $busqueda = trim($request->q);

                $query->where('nombre', 'LIKE', "%{$busqueda}%");

            })

            // Contar productos activos por categoría principal
            ->withCount([
                'productosComoPrincipal as productos_count' => function ($query) {
                    $query->where('activo', 1)
                        ->whereNull('deleted_at');
                }
            ])

            ->orderBy('nombre')
            ->get();

        return view('tienda.categorias.index', compact('categorias'));
    }

    public function show($slug)
    {
        $categoria = Categoria::query()
            ->where('slug', $slug)
            ->where('activo', 1)
            ->firstOrFail();

        $productos = Producto::with([
                'marca',
                'categoriaPrincipal',
                'imagenPrincipal',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
            ->where('id_categoria_principal', $categoria->id_categoria)
            ->orderByDesc('created_at')
            ->get();

        // ✅ Favoritos (para que funcione el corazón en esta vista)
        $favoritosIds = Favorito::where(function ($query) {
            if (auth()->check()) {
                $query->where('id_usuario', auth()->id());
            } else {
                $query->where('session_id', session()->getId());
            }
        })
        ->pluck('id_producto')
        ->toArray();

        return view('tienda.categorias.show', compact(
            'categoria',
            'productos',
            'favoritosIds'           // ← Agregado
        ));
    }
}