<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\CarruselItem;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
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
            ->map(function ($item) {
                $item->destino_url = $this->resolverDestinoCarrusel($item);
                return $item;
            });

        $categoriasHome = Categoria::query()
            ->where('activo', 1)
            ->withCount([
                'productos as productos_count' => function ($query) {
                    $query->where('productos.activo', 1);
                }
            ])
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->limit(4)
            ->get();

        $productosHome = Producto::query()
            ->where('activo', 1)
            ->with([
                'marca:id_marca,nombre,slug',
                'categoriaPrincipal:id_categoria,nombre,slug',
                'imagenPrincipal:id_imagen_producto,id_producto,ruta',
            ])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $marcasHome = Marca::query()
            ->where('activo', 1)
            ->withCount([
                'productos as productos_count' => function ($query) {
                    $query->where('productos.activo', 1);
                }
            ])
            ->orderByDesc('productos_count')
            ->orderBy('nombre')
            ->limit(4)
            ->get();

        return view('tienda.home.index', compact(
            'carruselItems',
            'categoriasHome',
            'productosHome',
            'marcasHome'
        ));
    }

    private function resolverDestinoCarrusel(CarruselItem $item): string
    {
        if ($item->tipo_destino === 'url' && $item->url_destino) {
            return $item->url_destino;
        }

        if ($item->tipo_destino === 'producto' && $item->producto?->slug) {
            return route('tienda.productos.show', $item->producto->slug);
        }

        if ($item->tipo_destino === 'categoria' && $item->categoria?->slug) {
            return route('tienda.categorias.show', $item->categoria->slug);
        }

        return route('tienda.productos.index');
    }
}