<?php

namespace App\Http\Controllers\Tienda;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Favorito;

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
                        ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                        ->orWhereHas('marca', function ($marca) use ($busqueda) {

                            $marca->where('nombre', 'LIKE', "%{$busqueda}%");

                        })
                        ->orWhereHas('categoriaPrincipal', function ($categoria) use ($busqueda) {

                            $categoria->where('nombre', 'LIKE', "%{$busqueda}%");

                        });

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

        // ✅ Favoritos (para que el corazón sepa qué productos están guardados)
        $favoritosIds = Favorito::where(function ($query) {
          if (Auth::check()) {
    $query->where('id_usuario', Auth::id());
} else {
    $query->where('session_id', session()->getId());
}
        })
        ->pluck('id_producto')
        ->toArray();

        return view('tienda.productos.index', compact(
            'productos',
            'categorias',
            'marcas',
            'favoritosIds'          // ← Agregado
        ));
    }

 public function show($slug)
{
    /** @var \App\Models\Producto $producto */
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

    // ✅ Favoritos en la vista de detalle
    $favoritosIds = Favorito::where(function ($query) {

        if (Auth::check()) {

            $query->where('id_usuario', Auth::id());

        } else {

            $query->where('session_id', session()->getId());

        }

    })
    ->pluck('id_producto')
    ->toArray();

    return view('tienda.productos.show', compact(
        'producto',
        'relacionados',
        'favoritosIds'
    ));
}

    public function sugerencias(Request $request)
    {
        $q = trim($request->q);

        if (!$q) {
            return response()->json([]);
        }

        $productos = Producto::query()
            ->with([
                'marca',
                'imagenPrincipal',
            ])
            ->where('activo', 1)
            ->where(function ($query) use ($q) {

                $query->where(
                    'nombre',
                    'LIKE',
                    "%{$q}%"
                );

            })
            ->limit(6)
            ->get()
            ->map(function ($producto) {

                return [

                    'nombre' => $producto->nombre,

                    'slug' => $producto->slug,

                    'precio' => number_format(
                        $producto->precio,
                        2
                    ),

                    'marca' => $producto->marca?->nombre,

                    'imagen' => $producto->imagenPrincipal?->ruta
                        ? asset('storage/' . $producto->imagenPrincipal->ruta)
                        : asset('assets/img/no-image.png'),

                    'url' => route(
                        'tienda.productos.show',
                        $producto->slug
                    ),

                ];

            });

        return response()->json($productos);
    }
}