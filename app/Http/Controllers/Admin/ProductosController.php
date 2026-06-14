<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\ImagenProducto;
use App\Models\TipoVariante;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class ProductosController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Producto::with(['marca', 'categoriaPrincipal', 'imagenPrincipal'])
            ->orderBy('nombre')
            ->get();

        return view('admin.productos.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
public function create()
{
    $marcas = Marca::where('activo', 1)
        ->orderBy('nombre')
        ->get();

    $categorias = Categoria::where('activo', 1)
        ->orderBy('nombre')
        ->get();

    $productosRelacionados = Producto::with('imagenPrincipal')
        ->whereNull('deleted_at')
        ->orderBy('nombre')
        ->get();

    $tiposVariantes = TipoVariante::with('opcionesActivas')
        ->where('activo', 1)
        ->orderBy('nombre')
        ->get();

    return view('admin.productos.create', compact(
        'marcas',
        'categorias',
        'productosRelacionados',
        'tiposVariantes'
    ));
}

public function store(Request $request)
{
    $request->validate([
        'id_marca' => [
            'nullable',
            Rule::exists('marcas', 'id_marca')->where(fn ($q) => $q->where('activo', 1)),
        ],

        'nombre' => 'required|string|max:190',
        'slug' => 'nullable|string|max:200',

        'codigo' => [
            'nullable',
            'string',
            'max:60',
            Rule::unique('productos', 'codigo')->whereNull('deleted_at'),
        ],

        'sku' => [
            'nullable',
            'string',
            'max:80',
            Rule::unique('productos', 'sku')->whereNull('deleted_at'),
        ],

        'descripcion' => 'nullable|string',

        'precio' => 'required|integer|min:0',

        'descuento_activo' => 'nullable|boolean',

        'precio_descuento' => [
            'nullable',
            'required_if:descuento_activo,1',
            'integer',
            'min:0',
            'lt:precio',
        ],

        'descuento_inicio' => [
            'nullable',
            'required_if:descuento_activo,1',
            'date',
        ],

        'descuento_fin' => [
            'nullable',
            'required_if:descuento_activo,1',
            'date',
            'after_or_equal:descuento_inicio',
        ],

        'stock_actual' => 'nullable|integer|min:0',

        'activo' => 'nullable|boolean',
        'destacado' => 'nullable|boolean',

        'id_categoria_principal' => [
            'nullable',
            Rule::exists('categorias', 'id_categoria')->where(fn ($q) => $q->where('activo', 1)),
        ],

        'categorias_adicionales' => 'nullable|array',
        'categorias_adicionales.*' => [
            'integer',
            'distinct',
            Rule::exists('categorias', 'id_categoria')->where(fn ($q) => $q->where('activo', 1)),
        ],

        'relacionados' => 'nullable|array',
        'relacionados.*' => [
            'integer',
            'distinct',
            Rule::exists('productos', 'id_producto')->whereNull('deleted_at'),
        ],

        'imagenes' => 'nullable|array',
        'imagenes.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        'principal_index' => 'nullable|integer|min:0',

        'usa_variantes' => 'nullable|boolean',

        'id_tipo_variante' => [
            'nullable',
            'required_if:usa_variantes,1',
            Rule::exists('tipos_variantes', 'id_tipo_variante')->where(fn ($q) => $q->where('activo', 1)),
        ],

        'variantes' => 'nullable|array',
        'variantes.*.id_opcion_variante' => [
            'nullable',
            Rule::exists('opciones_variantes', 'id_opcion_variante')->where(fn ($q) => $q->where('activo', 1)),
        ],
        'variantes.*.nombre' => 'nullable|string|max:120',
        'variantes.*.sku' => 'nullable|string|max:80',
        'variantes.*.precio' => 'nullable|integer|min:0',
        'variantes.*.stock_actual' => 'nullable|integer|min:0',
        'variantes.*.activo' => 'nullable|boolean',
        'variantes.*.es_principal' => 'nullable|boolean',
    ], [
        'precio_descuento.lt' => 'El precio con descuento debe ser menor que el precio normal.',
        'id_tipo_variante.required_if' => 'Debes seleccionar el tipo de variante.',
    ]);

    $usaVariantes = $request->boolean('usa_variantes');

    if ($usaVariantes) {
        $this->validarVariantesProducto($request);
    }

    $rutasSubidas = [];

    try {
        $slugGenerado = $this->resolverSlug(
            $request->input('slug'),
            $request->input('nombre')
        );

        $this->validarSlugRequerido($slugGenerado);
        $this->validarSlugUnico($slugGenerado);

        $categoriasAdicionales = $this->normalizarIds($request->input('categorias_adicionales', []));
        $relacionados = $this->normalizarIds($request->input('relacionados', []));

        $idCategoriaPrincipal = $request->filled('id_categoria_principal')
            ? (int) $request->id_categoria_principal
            : null;

        $categoriasSync = $categoriasAdicionales;

        if ($idCategoriaPrincipal !== null) {
            $categoriasSync[] = $idCategoriaPrincipal;
        }

        $categoriasSync = collect($categoriasSync)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $rutasSubidas[] = $imagen->store('productos', 'public');
            }
        }

        DB::transaction(function () use (
            $request,
            $slugGenerado,
            $categoriasSync,
            $relacionados,
            $rutasSubidas,
            $idCategoriaPrincipal,
            $usaVariantes
        ) {
            $item = Producto::create([
                'id_marca' => $request->filled('id_marca') ? $request->id_marca : null,
                'nombre' => trim($request->nombre),
                'slug' => $slugGenerado,
                'codigo' => $this->nullIfBlank($request->codigo),
                'sku' => $this->nullIfBlank($request->sku),
                'descripcion' => $this->nullIfBlank($request->descripcion),

                // Si usa variantes, el producto padre pierde precio propio
                'precio' => $usaVariantes ? 0 : $request->precio,

                'descuento_activo' => $usaVariantes
                    ? 0
                    : ($request->boolean('descuento_activo') ? 1 : 0),

                'precio_descuento' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->precio_descuento)
                    : null,

                'descuento_inicio' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->descuento_inicio)
                    : null,

                'descuento_fin' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->descuento_fin)
                    : null,

                // Si usa variantes, el stock vive en las variantes
                'stock_actual' => $usaVariantes ? 0 : $request->stock_actual,

                'usa_variantes' => $usaVariantes ? 1 : 0,
                'id_tipo_variante' => $usaVariantes ? $request->id_tipo_variante : null,

                'activo' => $request->boolean('activo') ? 1 : 0,
                'destacado' => $request->boolean('destacado') ? 1 : 0,

                'id_categoria_principal' => $idCategoriaPrincipal,
            ]);

            if ($usaVariantes) {
                $variantes = collect($request->input('variantes', []))
                    ->filter(function ($variante) {
                        return !empty($variante['id_opcion_variante'])
                            || !empty($variante['nombre']);
                    })
                    ->values();

         $indicePrincipal = $variantes->search(function ($variante) {
    return !empty($variante['es_principal']) && !empty($variante['activo']);
});

if ($indicePrincipal === false) {
    $indicePrincipal = $variantes->search(function ($variante) {
        return !empty($variante['activo']);
    });
}

foreach ($variantes as $index => $variante) {
    $esPrincipal = $index === $indicePrincipal;
    
                    ProductoVariante::create([
                        'id_producto' => $item->id_producto,
                        'id_opcion_variante' => $variante['id_opcion_variante'] ?? null,
                        'nombre' => $this->nullIfBlank($variante['nombre'] ?? null) ?? 'Variante',
                        'sku' => $this->nullIfBlank($variante['sku'] ?? null),

                        // La variante NO hereda precio del producto padre
                        'precio' => $this->nullIfBlank($variante['precio'] ?? null) ?? 0,

                        'stock_actual' => (int) ($variante['stock_actual'] ?? 0),
                        'activo' => !empty($variante['activo']) ? 1 : 0,
                        'es_principal' => $esPrincipal ? 1 : 0,
                    ]);
                }

                if (!$item->variantes()->where('es_principal', 1)->exists()) {
                    $primeraVariante = $item->variantes()
                        ->where('activo', 1)
                        ->orderBy('id_producto_variante')
                        ->first();

                    if (!$primeraVariante) {
                        $primeraVariante = $item->variantes()
                            ->orderBy('id_producto_variante')
                            ->first();
                    }

                    if ($primeraVariante) {
                        $primeraVariante->update([
                            'es_principal' => 1,
                        ]);
                    }
                }
            }

            $item->categorias()->sync($categoriasSync);

            $relacionados = collect($relacionados)
                ->reject(fn ($id) => $id === (int) $item->id_producto)
                ->values()
                ->toArray();

            $item->relacionados()->sync($relacionados);

            if (!empty($rutasSubidas)) {
                $principalIndex = (int) $request->input('principal_index', 0);

                if ($principalIndex < 0 || $principalIndex >= count($rutasSubidas)) {
                    $principalIndex = 0;
                }

                foreach ($rutasSubidas as $index => $ruta) {
                    ImagenProducto::create([
                        'id_producto' => $item->id_producto,
                        'ruta' => $ruta,
                        'es_principal' => $index === $principalIndex ? 1 : 0,
                        'orden' => $index + 1,
                    ]);
                }

                if (!$item->imagenes()->where('es_principal', 1)->exists()) {
                    $primeraImagen = $item->imagenes()->orderBy('orden')->first();

                    if ($primeraImagen) {
                        $primeraImagen->update(['es_principal' => 1]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');

    } catch (ValidationException $e) {
        $this->eliminarArchivosSiExisten($rutasSubidas);
        throw $e;

    } catch (\Exception $e) {
        $this->eliminarArchivosSiExisten($rutasSubidas);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error al crear el producto.');
    }
}
    /* ============================================
       👁️ VER
    ============================================ */

public function show(string $id)
{
    $item = Producto::with([
        'marca',
        'categoriaPrincipal',
        'categorias',
        'imagenes',
        'imagenPrincipal',

        'tipoVariante',
        'variantes',
        'variantes.opcion',

        'relacionados.imagenPrincipal',
    ])->findOrFail($id);

    return view('admin.productos.show', compact('item'));
}
    /* ============================================
       ✏️ EDITAR
    ============================================ */
public function edit(string $id)
{
    $item = Producto::with([
        'categorias',
        'relacionados',
        'imagenes',
        'tipoVariante',
        'variantes.opcion',
    ])->findOrFail($id);

    $marcas = Marca::where(function ($query) use ($item) {
            $query->where('activo', 1);

            if ($item->id_marca) {
                $query->orWhere('id_marca', $item->id_marca);
            }
        })
        ->orderBy('nombre')
        ->get();

    $idsCategoriasActuales = $item->categorias->pluck('id_categoria')->toArray();

    $categorias = Categoria::where(function ($query) use ($item, $idsCategoriasActuales) {
            $query->where('activo', 1);

            if ($item->id_categoria_principal) {
                $query->orWhere('id_categoria', $item->id_categoria_principal);
            }

            if (!empty($idsCategoriasActuales)) {
                $query->orWhereIn('id_categoria', $idsCategoriasActuales);
            }
        })
        ->orderBy('nombre')
        ->get();

    $productosRelacionados = Producto::with('imagenPrincipal')
        ->whereNull('deleted_at')
        ->where('id_producto', '!=', $item->id_producto)
        ->orderBy('nombre')
        ->get();

 $tiposVariantes = TipoVariante::with('opcionesActivas')
    ->where(function ($query) use ($item) {
        $query->where('activo', 1);

        if ($item->id_tipo_variante) {
            $query->orWhere('id_tipo_variante', $item->id_tipo_variante);
        }
    })
    ->orderBy('nombre')
    ->get();
    return view('admin.productos.edit', compact(
        'item',
        'marcas',
        'categorias',
        'productosRelacionados',
        'tiposVariantes'
    ));
}
public function update(Request $request, string $id)
{
    $item = Producto::with(['imagenes', 'categorias', 'variantes'])->findOrFail($id);
    $idsCategoriasActuales = $item->categorias->pluck('id_categoria')->toArray();

    $request->validate([
        'id_marca' => [
            'nullable',
            Rule::exists('marcas', 'id_marca')->where(function ($q) use ($item) {
                $q->where('activo', 1);

                if ($item->id_marca) {
                    $q->orWhere('id_marca', $item->id_marca);
                }
            }),
        ],

        'nombre' => 'required|string|max:190',
        'slug' => 'nullable|string|max:200',

        'codigo' => [
            'nullable',
            'string',
            'max:60',
            Rule::unique('productos', 'codigo')
                ->ignore($id, 'id_producto')
                ->whereNull('deleted_at'),
        ],

        'sku' => [
            'nullable',
            'string',
            'max:80',
            Rule::unique('productos', 'sku')
                ->ignore($id, 'id_producto')
                ->whereNull('deleted_at'),
        ],

        'descripcion' => 'nullable|string',

        'precio' => 'required|integer|min:0',

        'descuento_activo' => 'nullable|boolean',

        'precio_descuento' => [
            'nullable',
            'required_if:descuento_activo,1',
            'integer',
            'min:0',
            'lt:precio',
        ],

        'descuento_inicio' => [
            'nullable',
            'required_if:descuento_activo,1',
            'date',
        ],

        'descuento_fin' => [
            'nullable',
            'required_if:descuento_activo,1',
            'date',
            'after_or_equal:descuento_inicio',
        ],

        'stock_actual' => 'nullable|integer|min:0',

        'activo' => 'nullable|boolean',
        'destacado' => 'nullable|boolean',

        'id_categoria_principal' => [
            'nullable',
            Rule::exists('categorias', 'id_categoria')->where(function ($q) use ($item) {
                $q->where('activo', 1);

                if ($item->id_categoria_principal) {
                    $q->orWhere('id_categoria', $item->id_categoria_principal);
                }
            }),
        ],

        'categorias_adicionales' => 'nullable|array',
        'categorias_adicionales.*' => [
            'integer',
            'distinct',
            Rule::exists('categorias', 'id_categoria')->where(function ($q) use ($idsCategoriasActuales) {
                $q->where('activo', 1);

                if (!empty($idsCategoriasActuales)) {
                    $q->orWhereIn('id_categoria', $idsCategoriasActuales);
                }
            }),
        ],

        'relacionados' => 'nullable|array',
        'relacionados.*' => [
            'integer',
            'distinct',
            Rule::exists('productos', 'id_producto')->whereNull('deleted_at'),
        ],

        'imagenes' => 'nullable|array',
        'imagenes.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

        'principal_index' => 'nullable|integer|min:0',
        'imagen_principal_tipo' => 'nullable|in:existente,nueva',
        'imagen_principal_existente' => 'nullable|integer',

        'imagenes_eliminadas' => 'nullable|array',
        'imagenes_eliminadas.*' => 'integer',

        'imagenes_existentes_orden' => 'nullable|array',
        'imagenes_existentes_orden.*' => 'integer',

        'usa_variantes' => 'nullable|boolean',

        'id_tipo_variante' => [
            'nullable',
            'required_if:usa_variantes,1',
            Rule::exists('tipos_variantes', 'id_tipo_variante')->where(fn ($q) => $q->where('activo', 1)),
        ],

        'variantes' => 'nullable|array',
        'variantes.*.id_producto_variante' => 'nullable|integer',
        'variantes.*.id_opcion_variante' => [
            'nullable',
            Rule::exists('opciones_variantes', 'id_opcion_variante')->where(fn ($q) => $q->where('activo', 1)),
        ],
        'variantes.*.nombre' => 'nullable|string|max:120',
        'variantes.*.sku' => 'nullable|string|max:80',
        'variantes.*.precio' => 'nullable|integer|min:0',
        'variantes.*.stock_actual' => 'nullable|integer|min:0',
        'variantes.*.activo' => 'nullable|boolean',
        'variantes.*.es_principal' => 'nullable|boolean',
    ], [
        'precio_descuento.lt' => 'El precio con descuento debe ser menor que el precio normal.',
        'id_tipo_variante.required_if' => 'Debes seleccionar el tipo de variante.',
    ]);

    $usaVariantes = $request->boolean('usa_variantes');

    if ($usaVariantes) {
        $this->validarVariantesProducto($request);
    }

    $rutasSubidas = [];
    $rutasFisicasAEliminar = [];

    try {
        $slugGenerado = $this->resolverSlug(
            $request->input('slug'),
            $request->input('nombre')
        );

        $this->validarSlugRequerido($slugGenerado);
        $this->validarSlugUnico($slugGenerado, (int) $id);

        $categoriasAdicionales = $this->normalizarIds($request->input('categorias_adicionales', []));
        $relacionados = $this->normalizarIds($request->input('relacionados', []));

        $relacionados = collect($relacionados)
            ->reject(fn ($relId) => $relId === (int) $item->id_producto)
            ->values()
            ->toArray();

        $idCategoriaPrincipal = $request->filled('id_categoria_principal')
            ? (int) $request->id_categoria_principal
            : null;

        $categoriasSync = $categoriasAdicionales;

        if ($idCategoriaPrincipal !== null) {
            $categoriasSync[] = $idCategoriaPrincipal;
        }

        $categoriasSync = collect($categoriasSync)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $rutasSubidas[] = $imagen->store('productos', 'public');
            }
        }

        DB::transaction(function () use (
            $request,
            $item,
            $slugGenerado,
            $categoriasSync,
            $relacionados,
            $rutasSubidas,
            $idCategoriaPrincipal,
            &$rutasFisicasAEliminar,
            $usaVariantes
        ) {
            $imagenesActualesIds = $item->imagenes
                ->pluck('id_imagen_producto')
                ->map(fn ($imgId) => (int) $imgId);

            $imagenesEliminadas = collect($request->input('imagenes_eliminadas', []))
                ->map(fn ($imgId) => (int) $imgId)
                ->intersect($imagenesActualesIds)
                ->unique()
                ->values();

            $imagenesExistentesOrden = collect($request->input('imagenes_existentes_orden', []))
                ->map(fn ($imgId) => (int) $imgId)
                ->reject(fn ($imgId) => $imagenesEliminadas->contains($imgId))
                ->intersect($imagenesActualesIds)
                ->unique()
                ->values();

            $imagenPrincipalExistente = $request->filled('imagen_principal_existente')
                ? (int) $request->imagen_principal_existente
                : null;

            if ($imagenPrincipalExistente && !$imagenesActualesIds->contains($imagenPrincipalExistente)) {
                $imagenPrincipalExistente = null;
            }

            if ($imagenPrincipalExistente && $imagenesEliminadas->contains($imagenPrincipalExistente)) {
                $imagenPrincipalExistente = null;
            }

            $item->update([
                'id_marca' => $request->filled('id_marca') ? $request->id_marca : null,
                'nombre' => trim($request->nombre),
                'slug' => $slugGenerado,
                'codigo' => $this->nullIfBlank($request->codigo),
                'sku' => $this->nullIfBlank($request->sku),
                'descripcion' => $this->nullIfBlank($request->descripcion),

                // Si usa variantes, el producto padre pierde precio propio
                'precio' => $usaVariantes ? 0 : $request->precio,

                'descuento_activo' => $usaVariantes
                    ? 0
                    : ($request->boolean('descuento_activo') ? 1 : 0),

                'precio_descuento' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->precio_descuento)
                    : null,

                'descuento_inicio' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->descuento_inicio)
                    : null,

                'descuento_fin' => (!$usaVariantes && $request->boolean('descuento_activo'))
                    ? $this->nullIfBlank($request->descuento_fin)
                    : null,

                // Si usa variantes, el stock vive en las variantes
                'stock_actual' => $usaVariantes ? 0 : $request->stock_actual,

                'usa_variantes' => $usaVariantes ? 1 : 0,
                'id_tipo_variante' => $usaVariantes ? $request->id_tipo_variante : null,

                'activo' => $request->boolean('activo') ? 1 : 0,
                'destacado' => $request->boolean('destacado') ? 1 : 0,

                'id_categoria_principal' => $idCategoriaPrincipal,
            ]);

            if ($usaVariantes) {
                $idsVariantesRecibidas = [];
        $variantes = collect($request->input('variantes', []))
    ->filter(function ($variante) {
        return !empty($variante['id_opcion_variante'])
            || !empty($variante['nombre']);
    })
    ->values();

$indicePrincipal = $variantes->search(function ($variante) {
    return !empty($variante['es_principal']) && !empty($variante['activo']);
});

if ($indicePrincipal === false) {
    $indicePrincipal = $variantes->search(function ($variante) {
        return !empty($variante['activo']);
    });
}

foreach ($variantes as $index => $variante) {
    $idVariante = $variante['id_producto_variante'] ?? null;
    $esPrincipal = $index === $indicePrincipal;

                    $dataVariante = [
                        'id_producto' => $item->id_producto,
                        'id_opcion_variante' => $variante['id_opcion_variante'] ?? null,
                        'nombre' => $this->nullIfBlank($variante['nombre'] ?? null) ?? 'Variante',
                        'sku' => $this->nullIfBlank($variante['sku'] ?? null),

                        // La variante NO hereda precio del padre
                        'precio' => $this->nullIfBlank($variante['precio'] ?? null) ?? 0,

                        'stock_actual' => (int) ($variante['stock_actual'] ?? 0),
                        'activo' => !empty($variante['activo']) ? 1 : 0,
                        'es_principal' => $esPrincipal ? 1 : 0,
                    ];

                    if ($idVariante) {
                        $productoVariante = ProductoVariante::where('id_producto', $item->id_producto)
                            ->where('id_producto_variante', $idVariante)
                            ->first();

                        if ($productoVariante) {
                            $productoVariante->update($dataVariante);
                            $idsVariantesRecibidas[] = $productoVariante->id_producto_variante;
                        }
                    } else {
                        $productoVariante = ProductoVariante::create($dataVariante);
                        $idsVariantesRecibidas[] = $productoVariante->id_producto_variante;
                    }
                }

                ProductoVariante::where('id_producto', $item->id_producto)
                    ->whereNotIn('id_producto_variante', $idsVariantesRecibidas)
                    ->delete();

                if (!$item->variantes()->where('es_principal', 1)->exists()) {
                    $primeraVariante = $item->variantes()
                        ->where('activo', 1)
                        ->orderBy('id_producto_variante')
                        ->first();

                    if (!$primeraVariante) {
                        $primeraVariante = $item->variantes()
                            ->orderBy('id_producto_variante')
                            ->first();
                    }

                    if ($primeraVariante) {
                        $primeraVariante->update([
                            'es_principal' => 1,
                        ]);
                    }
                }
            } else {
                ProductoVariante::where('id_producto', $item->id_producto)->delete();
            }

            $item->categorias()->sync($categoriasSync);
            $item->relacionados()->sync($relacionados);

            if ($imagenesEliminadas->isNotEmpty()) {
                $imagenesABorrar = $item->imagenes()
                    ->whereIn('id_imagen_producto', $imagenesEliminadas)
                    ->get();

                foreach ($imagenesABorrar as $imagen) {
                    if ($imagen->ruta) {
                        $rutasFisicasAEliminar[] = $imagen->ruta;
                    }

                    $imagen->delete();
                }
            }

            if ($imagenesExistentesOrden->isNotEmpty()) {
                foreach ($imagenesExistentesOrden as $index => $imagenId) {
                    $imagen = $item->imagenes()
                        ->where('id_imagen_producto', $imagenId)
                        ->first();

                    if ($imagen) {
                        $imagen->update([
                            'orden' => $index + 1,
                        ]);
                    }
                }
            }

            $nuevasImagenesCreadas = collect();
            $ordenBase = (int) ($item->imagenes()->max('orden') ?? 0);

            if (!empty($rutasSubidas)) {
                foreach ($rutasSubidas as $index => $ruta) {
                    $nueva = ImagenProducto::create([
                        'id_producto' => $item->id_producto,
                        'ruta' => $ruta,
                        'es_principal' => 0,
                        'orden' => $ordenBase + $index + 1,
                    ]);

                    $nuevasImagenesCreadas->push($nueva);
                }
            }

            $item->imagenes()->update(['es_principal' => 0]);

            $tipoPrincipal = $request->input('imagen_principal_tipo', 'existente');

            if ($tipoPrincipal === 'existente' && $imagenPrincipalExistente) {
                $principalExistente = $item->imagenes()
                    ->where('id_imagen_producto', $imagenPrincipalExistente)
                    ->first();

                if ($principalExistente) {
                    $principalExistente->update(['es_principal' => 1]);
                }
            } elseif ($tipoPrincipal === 'nueva' && $nuevasImagenesCreadas->isNotEmpty()) {
                $principalIndex = (int) $request->input('principal_index', 0);

                if ($principalIndex < 0 || $principalIndex >= $nuevasImagenesCreadas->count()) {
                    $principalIndex = 0;
                }

                $principalNueva = $nuevasImagenesCreadas->get($principalIndex);

                if ($principalNueva) {
                    $principalNueva->update(['es_principal' => 1]);
                }
            }

            if (!$item->imagenes()->where('es_principal', 1)->exists()) {
                $primeraImagen = $item->imagenes()->orderBy('orden')->first();

                if ($primeraImagen) {
                    $primeraImagen->update(['es_principal' => 1]);
                }
            }
        });

        $this->eliminarArchivosSiExisten(array_unique($rutasFisicasAEliminar));

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');

    } catch (ValidationException $e) {
        $this->eliminarArchivosSiExisten($rutasSubidas);
        throw $e;

    } catch (\Exception $e) {
        $this->eliminarArchivosSiExisten($rutasSubidas);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error al actualizar el producto.');
    }
}

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
public function destroy(string $id)
{
    try {

        $item = Producto::findOrFail($id);

        // eliminar variantes primero
        $item->variantes()->delete();

        $item->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');

    } catch (QueryException $e) {

        return redirect()
            ->route('admin.productos.index')
            ->with('error', 'No se pudo eliminar el producto porque tiene registros relacionados.');

    } catch (\Exception $e) {

        return redirect()
            ->route('admin.productos.index')
            ->with('error', 'Error al eliminar el producto.');
    }
}

    /* ============================================
       🔧 APOYO
    ============================================ */
    private function resolverSlug(?string $slug, string $nombre): ?string
    {
        $base = trim((string) ($slug ?: $nombre));
        $slugGenerado = Str::slug($base);

        return $slugGenerado !== '' ? $slugGenerado : null;
    }

    private function validarSlugRequerido(?string $slug): void
    {
        if (!$slug) {
            throw ValidationException::withMessages([
                'slug' => 'No se pudo generar un slug válido. Verifica el nombre del producto o escribe un slug manual.',
            ]);
        }
    }

    private function validarSlugUnico(?string $slug, ?int $ignorarId = null): void
    {
        if (!$slug) {
            return;
        }

        $query = Producto::where('slug', $slug)
            ->whereNull('deleted_at');

        if ($ignorarId) {
            $query->where('id_producto', '!=', $ignorarId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'El slug ya está en uso. Ingresa uno diferente.',
            ]);
        }
    }

    private function normalizarIds($ids): array
    {
        return collect($ids ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }
private function validarVariantesProducto(Request $request): void
{
    $variantes = collect($request->input('variantes', []))
        ->filter(function ($variante) {
            return !empty($variante['id_opcion_variante'])
                || !empty($variante['nombre']);
        })
        ->values();

    if ($variantes->isEmpty()) {
        throw ValidationException::withMessages([
            'variantes' => 'Debes agregar al menos una variante.',
        ]);
    }

    $variantesActivas = $variantes->filter(function ($variante) {
        return !empty($variante['activo']);
    })->values();

    if ($variantesActivas->isEmpty()) {
        throw ValidationException::withMessages([
            'variantes' => 'Debes activar al menos una variante.',
        ]);
    }

    $principalesActivas = $variantesActivas->filter(function ($variante) {
        return !empty($variante['es_principal']);
    });

    if ($principalesActivas->count() > 1) {
        throw ValidationException::withMessages([
            'variantes' => 'Solo puede existir una variante principal activa.',
        ]);
    }

    foreach ($variantesActivas as $index => $variante) {
        if (($variante['precio'] ?? '') === '') {
            throw ValidationException::withMessages([
                "variantes.$index.precio" => 'Cada variante activa debe tener su propio precio.',
            ]);
        }

        if (($variante['stock_actual'] ?? '') === '') {
            throw ValidationException::withMessages([
                "variantes.$index.stock_actual" => 'Cada variante activa debe tener su propio stock.',
            ]);
        }
    }
}
    private function nullIfBlank($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function eliminarArchivosSiExisten(array $rutas): void
    {
        foreach ($rutas as $ruta) {
            if ($ruta && Storage::disk('public')->exists($ruta)) {
                Storage::disk('public')->delete($ruta);
            }
        }
    }
}