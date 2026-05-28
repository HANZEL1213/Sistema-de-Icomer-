<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\CarruselItem;
use App\Models\Categoria;
use App\Models\Favorito;
use App\Models\Marca;
use App\Models\Producto;
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
        | Se muestran todas las categorías activas.
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
        | PRODUCTOS HOME
        |--------------------------------------------------------------------------
        | Se muestran máximo 24 productos activos para dividirlos en 2 filas.
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
        | Se muestran todas las marcas activas.
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

        $favoritosIds = Favorito::where(function ($query) {
            if (Auth::check()) {
                $query->where('id_usuario', Auth::id());
            } else {
                $query->where('session_id', session()->getId());
            }
        })
            ->pluck('id_producto')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

   return view('tienda.home.index', compact(
    'carruselItems',
    'categoriasHome',
    'productosHome',
    'productosFila1',
    'productosFila2',
    'marcasHome',
    'favoritosIds'
));
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVER DESTINO CARRUSEL
    |--------------------------------------------------------------------------
    */

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
            $item->categoria?->slug
        ) {
            return route(
                'tienda.categorias.show',
                $item->categoria->slug
            );
        }

        return route('tienda.productos.index');
    }
}