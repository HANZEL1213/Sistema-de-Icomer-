<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\CarruselItem;
use App\Models\Categoria;
use App\Models\Favorito;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | CARRUSEL
        |--------------------------------------------------------------------------
        */

        $carruselItems = CarruselItem::query()
            ->with([
                'producto:id_producto,slug',
                'categoria:id_categoria,slug',
            ])
            ->activos()
            ->vigentes()
            ->where('orden', '>', 0)
            ->ordenados()
            ->get()
            ->map(function (CarruselItem $item) {
                $item->destino_url = $this->resolverDestinoCarrusel($item);

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | CATEGORÍAS HOME
        |--------------------------------------------------------------------------
        */

        $categoriasHome = Categoria::query()
            ->where('activo', 1)
            ->withCount([
                'productos as productos_count' => function ($query) {
                    $query->where('productos.activo', 1);
                },
            ])
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS DESTACADOS
        |--------------------------------------------------------------------------
        */

        $productosDestacados = Producto::query()
            ->where('activo', 1)
            ->where('destacado', 1)
            ->with([
                'marca:id_marca,nombre,slug',
                'categoriaPrincipal:id_categoria,nombre,slug',
                'imagenPrincipal:id_imagen_producto,id_producto,ruta',
            ])
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS HOME - PRIMERA CARGA
        |--------------------------------------------------------------------------
        */

        $productosHome = Producto::query()
            ->where('activo', 1)
            ->with([
                'marca:id_marca,nombre,slug',
                'categoriaPrincipal:id_categoria,nombre,slug',
                'imagenPrincipal:id_imagen_producto,id_producto,ruta',
            ])
            ->orderByDesc('created_at')
            ->limit(24)
            ->get();

        $productosFila1 = $productosHome->take(12);
        $productosFila2 = $productosHome->skip(12)->take(12);

        /*
        |--------------------------------------------------------------------------
        | MARCAS HOME
        |--------------------------------------------------------------------------
        */

        $marcasHome = Marca::query()
            ->where('activo', 1)
            ->withCount([
                'productos as productos_count' => function ($query) {
                    $query->where('productos.activo', 1);
                },
            ])
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FAVORITOS IDS
        |--------------------------------------------------------------------------
        */

        $favoritosIds = $this->obtenerFavoritosIds();

        return view('tienda.home.index', compact(
            'carruselItems',
            'categoriasHome',
            'productosDestacados',
            'productosHome',
            'productosFila1',
            'productosFila2',
            'marcasHome',
            'favoritosIds'
        ));
    }

    public function productosAjax(Request $request)
    {
        $page = max((int) $request->get('page', 1), 1);
        $perPage = 24;

        $productos = Producto::query()
            ->where('activo', 1)
            ->with([
                'marca:id_marca,nombre,slug',
                'categoriaPrincipal:id_categoria,nombre,slug',
                'imagenPrincipal:id_imagen_producto,id_producto,ruta',
            ])
            ->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $favoritosIds = $this->obtenerFavoritosIds();

        $productosFila1 = $productos->take(12);
        $productosFila2 = $productos->skip(12)->take(12);

        return response()->json([
            'html_fila_1' => view('tienda.home.partials.productos-home-items', [
                'productos' => $productosFila1,
                'favoritosIds' => $favoritosIds,
            ])->render(),

            'html_fila_2' => view('tienda.home.partials.productos-home-items', [
                'productos' => $productosFila2,
                'favoritosIds' => $favoritosIds,
            ])->render(),

            'has_more' => $productos->count() === $perPage,
        ]);
    }

    private function obtenerFavoritosIds(): array
    {
        return Favorito::where(function ($query) {
            if (Auth::check()) {
                $query->where('id_usuario', Auth::id());
            } else {
                $query->where('session_id', session()->getId());
            }
        })
            ->pluck('id_producto')
            ->toArray();
    }

private function resolverDestinoCarrusel(CarruselItem $item): string
{
    if (
        $item->tipo_destino === 'url' &&
        $item->url_destino
    ) {
        return $item->url_destino;
    }

    if (
        $item->tipo_destino === 'producto' &&
        $item->producto?->slug
    ) {
        return route(
            'tienda.productos.show',
            $item->producto->slug
        );
    }

    if (
        $item->tipo_destino === 'categoria' &&
        $item->id_categoria
    ) {
        return route('tienda.productos.index', [
            'categoria' => $item->id_categoria
        ]);
    }

    return route('tienda.productos.index');
}
}