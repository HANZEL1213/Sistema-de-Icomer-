<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'variantePrincipal',
                'variantesActivas',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
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
            ->when($request->filled('categoria'), function ($query) use ($request) {
                $query->where('id_categoria_principal', $request->categoria);
            })
            ->when($request->filled('marca'), function ($query) use ($request) {
                $query->where('id_marca', $request->marca);
            })
            ->when($request->filled('orden'), function ($query) use ($request) {
                match ($request->orden) {
                    'precio_menor' => $query->orderBy('precio'),
                    'precio_mayor' => $query->orderByDesc('precio'),
                    'az' => $query->orderBy('nombre'),
                    default => $query->orderByDesc('created_at'),
                };
            }, function ($query) {
                $query->orderByDesc('created_at');
            })
            ->get();

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
            'favoritosIds'
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
                'tipoVariante',
                'variantePrincipal.opcion',
                'variantesActivas.opcion',

                'relacionados' => function ($query) {
                    $query->where('activo', 1)
                        ->whereNull('deleted_at');
                },
                'relacionados.marca',
                'relacionados.categoriaPrincipal',
                'relacionados.imagenPrincipal',
                'relacionados.tipoVariante',
                'relacionados.variantePrincipal.opcion',
                'relacionados.variantesActivas.opcion',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
            ->where('slug', $slug)
            ->firstOrFail();

        $varianteInicial = $producto->obtenerVarianteInicial();

        $relacionados = $producto->relacionados()
            ->with([
                'marca',
                'categoriaPrincipal',
                'imagenPrincipal',
                'tipoVariante',
                'variantePrincipal.opcion',
                'variantesActivas.opcion',
            ])
            ->where('activo', 1)
            ->whereNull('deleted_at')
            ->get();

        if ($relacionados->isEmpty() && $producto->id_categoria_principal) {
            $relacionados = Producto::with([
                    'marca',
                    'categoriaPrincipal',
                    'imagenPrincipal',
                    'tipoVariante',
                    'variantePrincipal.opcion',
                    'variantesActivas.opcion',
                ])
                ->where('activo', 1)
                ->whereNull('deleted_at')
                ->where('id_categoria_principal', $producto->id_categoria_principal)
                ->where('id_producto', '!=', $producto->id_producto)
                ->get();
        }

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
            'favoritosIds',
            'varianteInicial'
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
                'variantePrincipal',
            ])
            ->where('activo', 1)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'LIKE', "%{$q}%");
            })
            ->limit(6)
            ->get()
            ->map(function ($producto) {
                return [
                    'nombre' => $producto->nombre,
                    'slug' => $producto->slug,
                    'precio' => number_format($producto->precioVenta(), 2),
                    'marca' => $producto->marca?->nombre,
                    'imagen' => $producto->imagenPrincipal?->ruta
                        ? asset('storage/' . $producto->imagenPrincipal->ruta)
                        : asset('assets/img/no-image.png'),
                    'url' => route('tienda.productos.show', $producto->slug),
                ];
            });

        return response()->json($productos);
    }
}