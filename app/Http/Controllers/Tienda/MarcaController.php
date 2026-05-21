<?php

namespace App\Http\Controllers\Tienda;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Favorito;           // ← Agregado

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $marcas = Marca::query()
            ->where('activo', 1)

            // Buscar marca por nombre
            ->when($request->filled('q'), function ($query) use ($request) {
                $busqueda = trim($request->q);

                $query->where('nombre', 'LIKE', "%{$busqueda}%");
            })

            // Contar productos activos de la marca
            ->withCount([
                'productos as productos_count' => function ($query) {
                    $query->where('activo', 1)
                        ->whereNull('deleted_at');
                }
            ])

            ->orderBy('nombre')
            ->get();

        return view('tienda.marcas.index', compact('marcas'));
    }

    public function show($slug)
    {
        $marca = Marca::query()
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
            ->where('id_marca', $marca->id_marca)
            ->orderByDesc('created_at')
            ->get();

        // ✅ Favoritos (para que funcione el corazón)
        $favoritosIds = Favorito::where(function ($query) {
        if (Auth::check()) {
    $query->where('id_usuario', Auth::id());
} else {
    $query->where('session_id', session()->getId());
}
        })
        ->pluck('id_producto')
        ->toArray();

        return view('tienda.marcas.show', compact(
            'marca',
            'productos',
            'favoritosIds'           // ← Agregado
        ));
    }
}