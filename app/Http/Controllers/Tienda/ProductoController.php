<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::with([
                'marca',
                'categoriaPrincipal',
                'imagenPrincipal',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')

            // 🔎 Búsqueda
            ->when($request->filled('q'), function ($query) use ($request) {

                $busqueda = trim($request->q);

                $query->where(function ($q) use ($busqueda) {

                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");

                });

            })

            // 📂 Categoría
            ->when($request->filled('categoria'), function ($query) use ($request) {

                $query->where(
                    'id_categoria_principal',
                    $request->categoria
                );

            })

            // 🏷️ Marca
            ->when($request->filled('marca'), function ($query) use ($request) {

                $query->where(
                    'id_marca',
                    $request->marca
                );

            })

            // ↕️ Orden
            ->when($request->filled('orden'), function ($query) use ($request) {

                match ($request->orden) {

                    'precio_menor'
                        => $query->orderBy('precio'),

                    'precio_mayor'
                        => $query->orderByDesc('precio'),

                    'az'
                        => $query->orderBy('nombre'),

                    default
                        => $query->orderByDesc('created_at'),
                };

            }, function ($query) {

                $query->orderByDesc('created_at');

            })

            // 🔥 Scroll continuo
            ->get();

        return view('tienda.productos.index', compact(
            'productos',
            'categorias',
            'marcas'
        ));
    }

    public function show($slug)
    {
        $producto = Producto::with([
                'marca',
                'categoriaPrincipal',
                'categorias',
                'imagenes',
                'imagenPrincipal',

                'relacionados' => function ($query) {

                    $query->where('activo', 1)
                        ->whereNull('deleted_at');

                },

                'relacionados.marca',
                'relacionados.categoriaPrincipal',
                'relacionados.imagenPrincipal',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
            ->where('slug', $slug)
            ->firstOrFail();

        // 🔥 Productos relacionados
        $relacionados = $producto->relacionados()
            ->with([
                'marca',
                'categoriaPrincipal',
                'imagenPrincipal',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
            ->limit(4)
            ->get();

        // 📂 Fallback por categoría
        if ($relacionados->isEmpty() && $producto->id_categoria_principal) {

            $relacionados = Producto::with([
                    'marca',
                    'categoriaPrincipal',
                    'imagenPrincipal',
                ])
                ->where('activo', 1)
                ->whereNull('deleted_at')
                ->where(
                    'id_categoria_principal',
                    $producto->id_categoria_principal
                )
                ->where(
                    'id_producto',
                    '!=',
                    $producto->id_producto
                )
                ->limit(4)
                ->get();
        }

        return view('tienda.productos.show', compact(
            'producto',
            'relacionados'
        ));
    }
}