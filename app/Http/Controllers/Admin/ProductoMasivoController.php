<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarruselItem;
use App\Models\Categoria;
use App\Models\LoteMasivo;
use App\Models\Marca;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductoMasivoController extends Controller
{
    public function index()
    {
        $categorias = Categoria::where('activo', 1)->orderBy('nombre')->get(['id_categoria', 'nombre']);
        $marcas     = Marca::where('activo', 1)->orderBy('nombre')->get(['id_marca', 'nombre']);

        return view('admin.productos_masivo.index', compact('categorias', 'marcas'));
    }

    /**
     * Reemplaza a Fuente.consultar() del JS. Devuelve EXACTAMENTE el mismo
     * contrato que la simulación: items, total, idsFiltrados, totalPar,
     * proximoLote, pagina. No cambiar estas claves sin actualizar el JS.
     */
    public function listar(Request $request)
    {
        $data = $request->validate([
            'categoria' => 'required|integer|exists:categorias,id_categoria',
            'marca'     => 'required|integer|exists:marcas,id_marca',
            'q'         => 'nullable|string|max:190',
            'page'      => 'nullable|integer|min:1',
            'perPage'   => 'nullable|integer|min:1|max:200',
        ]);

        $page    = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? 50;
        $q       = trim($data['q'] ?? '');

        $base = Producto::query()
            ->where('id_categoria_principal', $data['categoria'])
            ->where('id_marca', $data['marca']);

        $totalPar = (clone $base)->count();


        // Igual que en crear(): se cuentan los productos con soft delete.
        $proximoLote = (int) (Producto::withTrashed()
            ->where('id_categoria_principal', $data['categoria'])
            ->where('id_marca', $data['marca'])
            ->max('numero_lote') ?? 0) + 1;


        $filtrada = (clone $base);
        if ($q !== '') {
            $digitos = preg_replace('/\D/', '', $q);

            $filtrada->where(function ($query) use ($q, $digitos) {
                $query->where('nombre', 'like', "%{$q}%");

                if ($digitos !== '') {
                    $query->orWhereRaw(
                        "LPAD(COALESCE(numero_lote, 0), 4, '0') LIKE ?",
                        ["%{$digitos}%"]
                    );
                }
            });
        }

        $total = (clone $filtrada)->count();

        $idsFiltrados = (clone $filtrada)
            ->orderBy('numero_lote')
            ->orderBy('id_producto')
            ->pluck('id_producto')
            ->all();

        $totalPaginas = max(1, (int) ceil($total / $perPage));
        $page         = min(max(1, $page), $totalPaginas);
        $desde        = ($page - 1) * $perPage;

            $idsConVariantes = (clone $filtrada)
            ->where('usa_variantes', 1)
            ->pluck('id_producto')
            ->all();

        $productos = $filtrada
            ->with('imagenPrincipal')
            ->orderBy('numero_lote')
            ->orderBy('id_producto')
            ->skip($desde)
            ->take($perPage)
            ->get();

        return response()->json([
            'items'        => $productos->map(fn (Producto $p) => $this->mapearProducto($p))->values(),
            'total'        => $total,
            'idsFiltrados' => $idsFiltrados,
            'totalPar'     => $totalPar,
            'proximoLote'  => $proximoLote,
            'pagina'       => $page,
            'idsConVariantes' => $idsConVariantes,
        ]);
    }

    private function mapearProducto(Producto $p): array
    {
        $imagen = $p->imagenPrincipal?->ruta;

        return [
            'id'              => $p->id_producto,
            'lote'            => (int) ($p->numero_lote ?? 0),
            'nombre'          => $p->nombre,
            'slug'            => $p->slug,
            'precio'          => (float) $p->precio,
            'precioDescuento' => $p->tienePromocionActiva() ? (float) $p->precio_descuento : null,
            'stock'           => (int) $p->stock_actual,
            'activo'          => (bool) $p->activo,
            'destacado'       => (bool) $p->destacado,
            'usaVariantes'    => (bool) $p->usa_variantes,
            'imagen'          => $imagen
                ? (Str::startsWith($imagen, ['http://', 'https://']) ? $imagen : asset('storage/' . $imagen))
                : null,
        ];
    }


        /**
     * RF-02 — Bulk Create. Crea una TANDA de productos (1 imagen = 1 producto).
     * El front manda tandas de máximo 20 archivos (php.ini: max_file_uploads).
     * `token` hace la petición idempotente: un reintento no duplica productos.
     *
     * Cada imagen se valida por separado dentro del loop: una imagen inválida
     * se salta y se reporta con su motivo, sin tumbar al resto de la tanda.
     *
     * La imagen se guarda en disco ANTES de la transacción y su ruta queda
     * en $rutaGuardada: si la transacción falla (la BD se deshace sola pero
     * el disco no), el catch borra esa imagen para que no quede huérfana.
     */
    public function crear(Request $request)
    {
        $data = $request->validate([
            'categoria' => 'required|integer|exists:categorias,id_categoria',
            'marca'     => 'required|integer|exists:marcas,id_marca',
            'token'     => 'required|string|max:100',
            'precio'    => 'required|numeric|min:1',
            'stock'     => 'required|integer|min:0',
            'archivos'  => 'required|array|min:1|max:20',
            // Las reglas de cada imagen (tipo, peso, dimensiones) se revisan
            // una por una en motivoArchivoInvalido().
        ]);

        // Idempotencia: un reintento con el mismo token devuelve el resultado ya guardado.
        $claveCache = 'masivo:tanda:' . $data['token'];
        if ($resultadoCacheado = Cache::get($claveCache)) {
            return response()->json($resultadoCacheado);
        }

        $categoria   = (int) $data['categoria'];
        $marca       = (int) $data['marca'];
        $marcaModelo = Marca::findOrFail($marca);
        $precio      = round((float) $data['precio'], 2);
        $stock       = (int) $data['stock'];
        $archivos    = $request->file('archivos', []);
        $usuarioId   = $this->usuarioIdActual();

           // Lock de aplicación por par categoría+marca: evita que dos tandas
        // simultáneas calculen el mismo próximo numero_lote (RF-02).
        // GET_LOCK devuelve 1 si lo consiguió; 0 si venció el tiempo de espera;
        // NULL si hubo un error. Solo con 1 se puede seguir.
        $nombreLock = "masivo_par_{$categoria}_{$marca}";
        $resultadoLock = DB::selectOne('SELECT GET_LOCK(?, 10) AS conseguido', [$nombreLock]);

        if ((int) ($resultadoLock->conseguido ?? 0) !== 1) {
            Log::warning('Carga masiva: no se consiguió el lock del par categoría+marca', [
                'lock'  => $nombreLock,
                'token' => $data['token'],
            ]);

            // No se guarda nada en caché: así un reintento con el mismo token
            // sí vuelve a intentarlo. El JS muestra este mensaje en cada imagen
            // de la tanda y sigue con la siguiente tanda.
            return response()->json([
                'message' => 'Hay otra carga en curso para esta categoría y marca. Esperá a que termine e intentá de nuevo.',
            ], 503);
        }

                $ok         = 0;
        $errores    = [];
        $primerLote = null;
        $ultimoLote = null;

        try {
            // Con el lock ya conseguido, se registra el lote en la bitácora.
            // Va dentro del try para que, si esto falla, el finally libere el lock.
            $loteMasivo = LoteMasivo::create([
                'operacion'        => 'crear',
                'id_categoria'     => $categoria,
                'id_marca'         => $marca,
                'id_usuario'       => $usuarioId,
                'total_solicitado' => count($archivos),
                'total_ok'         => 0,
                'total_error'      => 0,
                'detalle'          => null,
            ]);

            foreach ($archivos as $archivo) {
      
                $nombreArchivo = $archivo->getClientOriginalName();

                // Revisión individual: si esta imagen no sirve, se anota el
                // motivo y se pasa a la siguiente. Como el número de lote se
                // calcula dentro de la transacción (más abajo), una imagen
                // rechazada aquí NO consume número de lote.
                $motivoInvalido = $this->motivoArchivoInvalido($archivo);
                if ($motivoInvalido !== null) {
                    $errores[] = [
                        'nombre' => $nombreArchivo,
                        'motivo' => $motivoInvalido,
                    ];
                    continue;
                }

                // Ruta de la imagen ya guardada en disco (null = todavía no se guardó).
                $rutaGuardada = null;

                try {
                    // La imagen se guarda ANTES de la transacción, para que su
                    // ruta quede a mano si hay que borrarla.
                    $rutaGuardada = $archivo->store('productos', 'public');
                    if ($rutaGuardada === false) {
                        throw new \RuntimeException('No se pudo guardar la imagen en el disco.');
                    }

                    DB::transaction(function () use (
                        $categoria, $marca, $marcaModelo, $precio, $stock, $usuarioId, $loteMasivo, $rutaGuardada,
                        &$primerLote, &$ultimoLote
                    ) { 
                        
                         // withTrashed(): el índice único (categoría, marca, numero_lote)
                        // también incluye los productos con soft delete, así que hay que contarlos.
                        $siguienteLote = (int) (Producto::withTrashed()
                            ->where('id_categoria_principal', $categoria)
                            ->where('id_marca', $marca)
                            ->max('numero_lote') ?? 0) + 1;

                        $numeroFormateado = str_pad((string) $siguienteLote, 4, '0', STR_PAD_LEFT);
                        $slugBase = Str::slug($marcaModelo->nombre . ' ' . $numeroFormateado);
                        $slug = $slugBase;
                        $sufijo = 2;
                        while (Producto::withTrashed()->where('slug', $slug)->exists()) {
                            $slug = $slugBase . '-' . $sufijo++;
                        }

                        $producto = Producto::create([
                            'id_marca'               => $marca,
                            'nombre'                 => $marcaModelo->nombre . ' #' . $numeroFormateado,
                            'slug'                   => $slug,
                            'numero_lote'            => $siguienteLote,
                            'precio'                 => $precio,
                            // Nace en 0: registrarEntradaInventario() de abajo sube el stock
                            // Y registra el movimiento en la misma operación.
                            'stock_actual'           => 0,
                            'usa_variantes'          => 0,
                            'activo'                 => 1,
                            'destacado'              => 0,
                            'descuento_activo'       => 0,
                            'id_categoria_principal' => $categoria,
                        ]);

                        $producto->categorias()->attach($categoria, ['created_at' => now()]);

                        $producto->imagenes()->create([
                            'ruta'         => $rutaGuardada,
                            'es_principal' => 1,
                            'orden'        => 1,
                        ]);

                        if ($stock > 0) {
                            $producto->registrarEntradaInventario(
                                $stock,
                                'Carga masiva',
                                null,
                                null,
                                $usuarioId,
                                'Lote masivo #' . $loteMasivo->id_lote_masivo
                            );
                        }

                        if ($primerLote === null) {
                            $primerLote = $siguienteLote;
                        }
                        $ultimoLote = $siguienteLote;
                    });
                    $ok++;
                } catch (\Throwable $e) {
                    // Fallo inesperado (disco, base de datos): el error real se guarda
                    // en storage/logs/laravel.log y en pantalla sale un mensaje corto.
                    Log::error('Carga masiva: falló la creación de un producto', [
                        'archivo'   => $nombreArchivo,
                        'error'     => $e->getMessage(),
                        'ubicacion' => $e->getFile() . ':' . $e->getLine(),
                    ]);

                    // La base de datos se deshizo sola, pero la imagen ya estaba
                    // en disco: se borra para que no quede huérfana.
                    if ($rutaGuardada) {
                        try {
                            Storage::disk('public')->delete($rutaGuardada);
                        } catch (\Throwable $eBorrado) {
                            Log::warning('Carga masiva: no se pudo borrar una imagen huérfana', [
                                'ruta'  => $rutaGuardada,
                                'error' => $eBorrado->getMessage(),
                            ]);
                        }
                    }

                    $errores[] = [
                        'nombre' => $nombreArchivo,
                        'motivo' => 'no se pudo procesar la imagen',
                    ];
                }
            }
        } finally {
            DB::select('SELECT RELEASE_LOCK(?)', [$nombreLock]);
        }

        $loteMasivo->update([
            'total_ok'    => $ok,
            'total_error' => count($errores),
            'detalle'     => $errores ?: null,
        ]);

        $resultado = [
            'ok'         => $ok,
            'primerLote' => $primerLote,
            'ultimoLote' => $ultimoLote,
            'errores'    => $errores,
        ];
        Cache::put($claveCache, $resultado, now()->addHour());

        return response()->json($resultado);
    }

    private function motivoArchivoInvalido(UploadedFile $archivo): ?string
    {
        $minAncho = 200;
        $minAlto  = 200;

        // El archivo no llegó completo al servidor (corte de red, límite de PHP, etc.)
        if (!$archivo->isValid()) {
            return 'el archivo llegó incompleto o dañado al servidor';
        }

        // Tipo real (se mira el contenido, no solo la extensión) y peso.
        $validador = Validator::make(
            ['archivo' => $archivo],
            ['archivo' => 'mimes:jpeg,png,webp|max:8192'],
            [
                'archivo.mimes' => 'no es un JPG, PNG o WEBP real (puede ser otro formato con la extensión cambiada)',
                'archivo.max'   => 'pesa más de 8 MB',
            ]
        );
        if ($validador->fails()) {
            return $validador->errors()->first('archivo');
        }

        // Legibilidad y tamaño en píxeles.
        $info = @getimagesize($archivo->getRealPath());
        if ($info === false) {
            return 'la imagen está dañada y no se puede leer';
        }

        [$ancho, $alto] = $info;
        if ($ancho < $minAncho || $alto < $minAlto) {
            return "mide {$ancho}×{$alto} px (mínimo {$minAncho}×{$minAlto})";
        }

        return null;
    }

    /**
     * RF-03 — Bulk Edit. Solo se aplican las claves presentes en la petición
     * (mismo criterio que edLeerCambios() del JS: precio, stock, activo,
     * destacado, descuento). Cada producto es atómico: si algo falla para
     * ese producto, no se toca y se reporta en `errores`.
     */
public function editar(Request $request)
{
    $data = $request->validate([
        'ids'              => 'required|array|min:1',
        'ids.*'            => 'integer|distinct',
        'precio'           => 'nullable|numeric|min:1',
        'stock'            => 'nullable|array',
        'stock.modo'       => 'required_with:stock|in:fijar,sumar,restar',
        'stock.valor'      => 'required_with:stock|integer|min:0',
        'activo'           => 'nullable|boolean',
        'destacado'        => 'nullable|boolean',
        'descuento'        => 'nullable|array',
        'descuento.accion' => 'required_with:descuento|in:activar,quitar',
        'descuento.precio' => 'required_if:descuento.accion,activar|numeric|min:1',
        'descuento.inicio' => 'required_if:descuento.accion,activar|date',
        'descuento.fin'    => 'required_if:descuento.accion,activar|date|after:descuento.inicio',
    ]);

    $camposEditables = array_intersect_key($data, array_flip(['precio', 'stock', 'activo', 'destacado', 'descuento']));
    if (!$camposEditables) {
        return response()->json(['message' => 'Marcá al menos un campo para modificar.'], 422);
    }

    $productos = $this->cargarSeleccion($data['ids']);
    $editados  = [];
    $errores   = [];
    $aCero     = 0;
    $usuarioId = $this->usuarioIdActual();

    // El lote se crea ANTES del loop para tener su id y anotarlo en cada
    // movimiento de inventario. Los totales se completan al final.
    // Asume que toda la selección viene de un mismo par categoría+marca
    // (así la arma el front).
    $primero    = $productos->first();
    $loteMasivo = $primero
        ? LoteMasivo::create([
            'operacion'        => 'editar',
            'id_categoria'     => $primero->id_categoria_principal,
            'id_marca'         => $primero->id_marca,
            'id_usuario'       => $usuarioId,
            'total_solicitado' => count($data['ids']),
            'total_ok'         => 0,
            'total_error'      => 0,
            'detalle'          => null,
        ])
        : null;

    foreach ($productos as $producto) {
        try {
            $quedoACero = false;

            DB::transaction(function () use ($producto, $camposEditables, $usuarioId, $loteMasivo, &$quedoACero) {

                if ($producto->usa_variantes) {
                    $bloqueados = array_values(array_intersect(
                        ['precio', 'stock', 'descuento'],
                        array_keys($camposEditables)
                    ));

                    if ($bloqueados) {
                        throw new \RuntimeException(
                            'tiene variantes: ' . implode(', ', $bloqueados) . ' se edita en cada variante, no en bloque'
                        );
                    }
                }

                $precioActual = (float) $producto->precio;
                $precioFinal  = array_key_exists('precio', $camposEditables)
                    ? round((float) $camposEditables['precio'], 2)
                    : $precioActual;

                if (isset($camposEditables['stock'])) {
                    $modo   = $camposEditables['stock']['modo'];
                    $valor  = (int) $camposEditables['stock']['valor'];
                    $actual = (int) $producto->stock_actual;

                    if ($modo === 'fijar') {
                        $nuevo = $valor;
                    } elseif ($modo === 'sumar') {
                        $nuevo = $actual + $valor;
                    } else { // restar
                        $nuevo = max(0, $actual - $valor);
                        if ($actual - $valor < 0) {
                            $quedoACero = true;
                        }
                    }

                    if ($nuevo !== $actual) {
                        $producto->stock_actual = $nuevo;

                        // Sube = entrada, baja = salida. La cantidad es lo que
                        // realmente se movió (si restar llega a 0, es lo que había).
                        MovimientoInventario::create([
                            'id_producto'           => $producto->id_producto,
                            'tipo'                  => $nuevo > $actual ? 'entrada' : 'salida',
                            'cantidad'              => abs($nuevo - $actual),
                            'motivo'                => 'Edición masiva',
                            'id_usuario_realizador' => $usuarioId,
                            'notas'                 => 'Lote masivo #' . $loteMasivo->id_lote_masivo,
                        ]);
                    }
                }

                if (array_key_exists('activo', $camposEditables)) {
                    $producto->activo = (bool) $camposEditables['activo'];
                }
                if (array_key_exists('destacado', $camposEditables)) {
                    $producto->destacado = (bool) $camposEditables['destacado'];
                }

                if (isset($camposEditables['descuento'])) {
                    $descuento = $camposEditables['descuento'];
                    if ($descuento['accion'] === 'quitar') {
                        $producto->descuento_activo = 0;
                        $producto->precio_descuento = null;
                        $producto->descuento_inicio = null;
                        $producto->descuento_fin = null;
                    } else {
                        $precioDescuento = round((float) $descuento['precio'], 2);
                        if ($precioDescuento >= $precioFinal) {
                            throw new \RuntimeException(
                                "el descuento (₡{$precioDescuento}) no queda por debajo del precio (₡{$precioFinal})"
                            );
                        }
                        $producto->descuento_activo = 1;
                        $producto->precio_descuento = $precioDescuento;
                        $producto->descuento_inicio = $descuento['inicio'];
                        $producto->descuento_fin = $descuento['fin'];
                    }
                }

                // Si solo se cambia el precio (sin tocar el descuento), el descuento
                // que el producto ya tiene guardado debe seguir quedando por debajo.
                if (array_key_exists('precio', $camposEditables) && !isset($camposEditables['descuento'])) {
                    if ($producto->descuento_activo && $producto->precio_descuento !== null) {
                        $descuentoGuardado = (float) $producto->precio_descuento;

                        if ($descuentoGuardado >= $precioFinal) {
                            throw new \RuntimeException(
                                "el nuevo precio (₡{$precioFinal}) queda igual o por debajo del descuento que ya tiene (₡{$descuentoGuardado}). Quitá o ajustá el descuento primero"
                            );
                        }
                    }
                }

                if (array_key_exists('precio', $camposEditables)) {
                    $producto->precio = $precioFinal;
                }

                $producto->save();
            });

            if ($quedoACero) {
                $aCero++;
            }
            $editados[] = $producto->id_producto;
        } catch (\Throwable $e) {
            $errores[] = [
                'id'     => $producto->id_producto,
                'nombre' => $producto->nombre,
                'motivo' => $e->getMessage(),
            ];
        }
    }

    $avisos = $aCero
        ? [$aCero . ($aCero === 1 ? ' producto quedó' : ' productos quedaron') . ' con stock 0 (no se resta por debajo de cero).']
        : [];

    // Se completan los totales del lote creado antes del loop.
    $loteMasivo?->update([
        'total_ok'    => count($editados),
        'total_error' => count($errores),
        'detalle'     => $errores ?: null,
    ]);

    return response()->json([
        'solicitados' => count($data['ids']),
        'ok'          => count($editados),
        'idsEditados' => $editados,
        'errores'     => $errores,
        'avisos'      => $avisos,
    ]);
}
    /**
     * Interruptor "Activo" de la tabla: cambia el estado de UN producto.
     * No deja registro en la bitácora (lotes_masivos): esa tabla es solo
     * para operaciones masivas (crear, editar y borrar en bloque).
     */
    public function cambiarActivo(Request $request)
    {
        $data = $request->validate([
            'id'     => 'required|integer|exists:productos,id_producto',
            'activo' => 'required|boolean',
        ]);

        $producto         = Producto::findOrFail($data['id']);
        $producto->activo = (bool) $data['activo'];
        $producto->save();

        return response()->json([
            'id'     => $producto->id_producto,
            'activo' => (bool) $producto->activo,
        ]);
    }


    /**
     * Datos para el modal de borrado (cuántos, muestra de nombres, avisos de
     * carrusel). Se llama ANTES de eliminar(), al abrir el modal.
     */
    public function previsualizarBorrado(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|distinct',
        ]);

        $productos  = $this->cargarSeleccion($data['ids']);
        $enCarrusel = CarruselItem::where('tipo_destino', 'producto')
            ->whereIn('id_producto', $data['ids'])
            ->count();

        return response()->json([
            'total'        => $productos->count(),
            'muestra'      => $productos->pluck('nombre')->take(8)->values(),
            'enCarrusel'   => $enCarrusel,
            'conVariantes' => $productos->where('usa_variantes', true)->count(),
        ]);
    }
    /**
     * RF-04 — Bulk Delete definitivo (forceDelete). Antes de borrar,
     * desactiva los banners del carrusel que apunten a estos productos
     * (evita id_producto = NULL colgando de un banner activo). Los archivos
     * físicos se borran DESPUÉS del commit de la transacción.
     */
    public function eliminar(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
                        'ids.*' => 'integer|distinct',
        ]);

        $productos = $this->cargarSeleccion($data['ids'], ['imagenes']);
        $usuarioId = $this->usuarioIdActual();

        CarruselItem::where('tipo_destino', 'producto')
            ->whereIn('id_producto', $data['ids'])
            ->update(['activo_manual' => 0]);

        $categoriaRef = $productos->first()?->id_categoria_principal;
        $marcaRef     = $productos->first()?->id_marca;

        $idsEliminados = [];
        $rutasABorrar  = [];

        DB::transaction(function () use ($productos, &$idsEliminados, &$rutasABorrar) {
            foreach ($productos as $producto) {
                foreach ($producto->imagenes as $imagen) {
                    $rutasABorrar[] = $imagen->ruta;
                }
                $idsEliminados[] = $producto->id_producto;
                // Cascada de BD: imagenes_producto, movimientos_inventario,
                // favoritos, categorias_productos, productos_relacionados.
                // producto_variantes.
                $producto->forceDelete();
            }
        });

        // Un archivo que ya no exista en disco no se considera error.
        foreach ($rutasABorrar as $ruta) {
            Storage::disk('public')->delete($ruta);
        }

        if ($categoriaRef && $marcaRef) {
            LoteMasivo::create([
                'operacion'        => 'borrar',
                'id_categoria'     => $categoriaRef,
                'id_marca'         => $marcaRef,
                'id_usuario'       => $usuarioId,
                'total_solicitado' => count($data['ids']),
                'total_ok'         => count($idsEliminados),
                'total_error'      => 0,
                'detalle'          => null,
            ]);
        }

        return response()->json([
            'solicitados'   => count($data['ids']),
            'ok'            => count($idsEliminados),
            'idsEliminados' => $idsEliminados,
            'errores'       => [],
        ]);
    }



    /**
     * Trae los productos de la selección con UNA sola consulta y verifica que
     * todos existan (un producto borrado con soft delete cuenta como inexistente).
     * Reemplaza al `ids.*` => exists, que hacía una consulta por cada id.
     */
    private function cargarSeleccion(array $ids, array $relaciones = []): \Illuminate\Database\Eloquent\Collection
    {
        $ids       = array_map('intval', $ids);
        $productos = Producto::whereIn('id_producto', $ids)->with($relaciones)->get();

        if ($productos->count() !== count($ids)) {
            $faltantes = array_diff($ids, $productos->pluck('id_producto')->all());

            throw ValidationException::withMessages([
                'ids' => 'Algunos productos de la selección ya no existen (ids: '
                    . implode(', ', array_slice($faltantes, 0, 5))
                    . (count($faltantes) > 5 ? '…' : '')
                    . '). Recargá la lista e intentá de nuevo.',
            ]);
        }

        return $productos;
    }


        /**
     * Id del usuario con sesión activa. Si no hay ninguno, corta la petición
     * con 401 en vez de atribuir la acción a otro usuario (antes: `?? 1`).
     * El mensaje contiene "sesión expiró" para que el JS muestre el aviso
     * de recargar página.
     */
    private function usuarioIdActual(): int
    {
        $id = auth()->id();

        if ($id === null) {
            abort(401, 'Tu sesión expiró. Recargá la página (F5) e intentá de nuevo.');
        }

        return (int) $id;
    }

    }

