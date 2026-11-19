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
    /** Cantidad de filas (categorías) que se muestran en el home. */
    private const MAX_FILAS_HOME = 6;

    /** Productos por fila, tanto en la primera carga como en cada carga AJAX. */
    private const PRODUCTOS_POR_FILA = 12;

    /** Tope de páginas por fila para evitar peticiones absurdas. */
    private const MAX_PAGINAS_AJAX = 100;

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
            ->with($this->relacionesTarjeta())
            ->orderByDesc('created_at')
            ->orderByDesc('id_producto')
            ->limit(self::PRODUCTOS_POR_FILA)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FILAS POR CATEGORÍA - PRIMERA CARGA
        |--------------------------------------------------------------------------
        */

        $categoriasFilas = $categoriasHome
            ->where('productos_count', '>', 0)
            ->take(self::MAX_FILAS_HOME)
            ->values();

        $productosPorCategoria = collect();
        $filasTieneMas = [];

        foreach ($categoriasFilas as $cat) {
            [$lista, $hayMas] = $this->productosDeCategoria($cat->id_categoria);

            $productosPorCategoria->put($cat->id_categoria, $lista);
            $filasTieneMas[$cat->id_categoria] = $hayMas;
        }

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
            'categoriasFilas',
            'productosDestacados',
            'productosPorCategoria',
            'filasTieneMas',
            'marcasHome',
            'favoritosIds'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: MÁS PRODUCTOS DE UNA CATEGORÍA (scroll horizontal de cada fila)
    |--------------------------------------------------------------------------
    */

    public function productosCategoriaAjax(Request $request, int $categoria)
    {
        // Solo categorías activas; evita exponer productos de categorías ocultas.
        abort_unless(
            Categoria::where('id_categoria', $categoria)->where('activo', 1)->exists(),
            404
        );

        $page = min(max((int) $request->query('page', 1), 1), self::MAX_PAGINAS_AJAX);

        [$productos, $hasMore] = $this->productosDeCategoria($categoria, $page);

        return response()->json([
            'html' => view('tienda.home.partials.productos-home-items', [
                'productos'    => $productos,
                'favoritosIds' => $this->obtenerFavoritosIds(),
            ])->render(),
            'has_more' => $hasMore,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS PRIVADOS
    |--------------------------------------------------------------------------
    */

    /**
     * Relaciones que necesita cada tarjeta de producto del home.
     * Un solo lugar para que todas las consultas carguen lo mismo
     * (incluye variantes, que el partial usa para precio y stock).
     */
    private function relacionesTarjeta(): array
    {
        return [
            'marca:id_marca,nombre,slug',
            'categoriaPrincipal:id_categoria,nombre,slug',
            'imagenPrincipal:id_imagen_producto,id_producto,ruta',
            'variantePrincipal',
            'variantesActivas',
        ];
    }

    /**
     * Productos de una categoría, paginados de a PRODUCTOS_POR_FILA.
     * La primera carga y el AJAX usan este mismo método, así el orden
     * es idéntico y nunca se repiten ni se saltan productos.
     *
     * Devuelve: [Collection $productos, bool $hayMas]
     */
    private function productosDeCategoria(int $idCategoria, int $page = 1): array
    {
        $perPage = self::PRODUCTOS_POR_FILA;

        // Se piden perPage + 1 para saber si hay más sin hacer un count extra.
        $lista = Producto::query()
            ->where('activo', 1)
            ->where('id_categoria_principal', $idCategoria)
            ->with($this->relacionesTarjeta())
            ->orderByDesc('created_at')
            ->orderByDesc('id_producto')
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        return [$lista->take($perPage)->values(), $lista->count() > $perPage];
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
        if ($item->tipo_destino === 'url' && $item->url_destino) {
            return $item->url_destino;
        }

        if ($item->tipo_destino === 'producto' && $item->producto?->slug) {
            return route('tienda.productos.show', $item->producto->slug);
        }

        if ($item->tipo_destino === 'categoria' && $item->id_categoria) {
            return route('tienda.productos.index', [
                'categoria' => $item->id_categoria,
            ]);
        }

        return route('tienda.productos.index');
    }
}