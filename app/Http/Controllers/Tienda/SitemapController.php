<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Página principal
        $urls[] = [
            'loc' => url('/'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Listados generales
        $urls[] = ['loc' => route('tienda.productos.index'), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => route('tienda.categorias.index'), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => route('tienda.marcas.index'), 'changefreq' => 'weekly', 'priority' => '0.8'];

        // Categorías activas
        foreach (Categoria::where('activo', 1)->get() as $categoria) {
            $urls[] = [
                'loc' => route('tienda.categorias.show', $categoria->slug),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Marcas activas
        foreach (Marca::where('activo', 1)->get() as $marca) {
            $urls[] = [
                'loc' => route('tienda.marcas.show', $marca->slug),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Productos activos
        foreach (Producto::where('activo', 1)->get() as $producto) {
            $urls[] = [
                'loc' => route('tienda.productos.show', $producto->slug),
                'lastmod' => $producto->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        $xml = view('sitemap.index', compact('urls'))->render();

        return Response::make($xml, 200)->header('Content-Type', 'text/xml');
    }
}