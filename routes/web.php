<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS - TIENDA
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Tienda\{
    HomeController,
    ProductoController,
    CategoriaController,
    MarcaController,
    CarritoController,
    CheckoutController,
    PedidoController,
    PagoPedidoController
};

/*
|--------------------------------------------------------------------------
| CONTROLLERS - ADMIN
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\{
    DashboardController,
    CarruselItemsController,
    CategoriasController,
    ConfiguracionController,
    CuponesController,
    MovimientosInventarioController,
    MarcasController,
    PedidosController,
    PagosPedidosController,
    ProductosController,
    ImagenesProductoController,
    ProductosRelacionadosController,
    UsuariosController,
    RolesController,
    VentasLocalesController,
    PagosVentasLocalesController,
    ZonasEnvioController,
    VentasController,
    UsosCuponesController
};

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS - TIENDA
|--------------------------------------------------------------------------
| Aquí va toda la navegación del cliente:
| home, catálogo, categorías, marcas, carrito, checkout y pedidos.
|--------------------------------------------------------------------------
*/
Route::name('tienda.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */
    Route::get('/', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Productos
    |--------------------------------------------------------------------------
    */
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::get('{slug}', [ProductoController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Categorías
    |--------------------------------------------------------------------------
    */
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->name('index');
        Route::get('{slug}', [CategoriaController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Marcas
    |--------------------------------------------------------------------------
    */
    Route::prefix('marcas')->name('marcas.')->group(function () {
        Route::get('/', [MarcaController::class, 'index'])->name('index');
        Route::get('{slug}', [MarcaController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Carrito
    |--------------------------------------------------------------------------
    */
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('confirmacion', [CheckoutController::class, 'confirmacion'])->name('confirmacion');
    });

    /*
    |--------------------------------------------------------------------------
    | Pedidos del cliente
    |--------------------------------------------------------------------------
    */
    Route::prefix('pedido')->name('pedidos.')->group(function () {
        Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('mis');
        Route::get('{codigo}', [PedidoController::class, 'show'])->name('show');
        Route::get('{codigo}/seguimiento', [PedidoController::class, 'seguimiento'])->name('seguimiento');
    });

    /*
    |--------------------------------------------------------------------------
    | Pago de pedido
    |--------------------------------------------------------------------------
    */
    Route::prefix('pedido')->name('pagos.')->group(function () {
        Route::get('{pedido}/pago', [PagoPedidoController::class, 'index'])->name('index');
    });
});

/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS - ADMIN
|--------------------------------------------------------------------------
| Aquí va toda la gestión administrativa del sistema:
| dashboard, CRUDs, pedidos, ventas, inventario, usuarios, etc.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CRUD principales
    |--------------------------------------------------------------------------
    */
    Route::resource('carrusel-items', CarruselItemsController::class);
    Route::resource('categorias', CategoriasController::class);
    Route::resource('cupones', CuponesController::class);
    Route::resource('marcas', MarcasController::class);
    Route::resource('productos', ProductosController::class);
    Route::resource('usuarios', UsuariosController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('configuracion', ConfiguracionController::class);

    /*
    |--------------------------------------------------------------------------
    | Inventario
    |--------------------------------------------------------------------------
    */
    Route::resource('inventario-movimientos', MovimientosInventarioController::class)
        ->only(['index', 'create', 'store', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Pedidos online
    |--------------------------------------------------------------------------
    */
    Route::resource('pedidos', PedidosController::class)
        ->only(['index', 'show']);

    Route::get('pedidos/{id}/verificar', [PedidosController::class, 'verificar'])
        ->name('pedidos.verificar');

    Route::patch('pedidos/{id}/aprobar-pago', [PedidosController::class, 'aprobarPago'])
        ->name('pedidos.aprobar-pago');

    Route::patch('pedidos/{id}/rechazar-pago', [PedidosController::class, 'rechazarPago'])
        ->name('pedidos.rechazar-pago');

    Route::patch('pedidos/{id}/actualizar-estado', [PedidosController::class, 'actualizarEstado'])
        ->name('pedidos.actualizar-estado');

    /*
    |--------------------------------------------------------------------------
    | Pagos de pedidos
    |--------------------------------------------------------------------------
    */
    Route::resource('pagos-pedidos', PagosPedidosController::class)
        ->only(['index', 'show', 'update']);

    /*
    |--------------------------------------------------------------------------
    | Ventas físicas
    |--------------------------------------------------------------------------
    */
    Route::resource('ventas-locales', VentasLocalesController::class);

    Route::resource('pagos-ventas-locales', PagosVentasLocalesController::class)
        ->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Zonas de envío
    |--------------------------------------------------------------------------
    */
    Route::resource('zonas-envio', ZonasEnvioController::class);

    Route::get('zonas-envio/cantones/{id_provincia}', [ZonasEnvioController::class, 'obtenerCantones'])
        ->name('zonas-envio.cantones');

    Route::get('zonas-envio/distritos/{id_canton}', [ZonasEnvioController::class, 'obtenerDistritos'])
        ->name('zonas-envio.distritos');

    /*
    |--------------------------------------------------------------------------
    | Reportes y consultas
    |--------------------------------------------------------------------------
    */
    Route::resource('ventas', VentasController::class)
        ->only(['index']);

    Route::resource('usos-cupones', UsosCuponesController::class)
        ->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Extras de productos
    |--------------------------------------------------------------------------
    */
    Route::prefix('productos/{producto}')->name('productos.')->group(function () {

        // Imágenes del producto
        Route::get('imagenes', [ImagenesProductoController::class, 'index'])->name('imagenes.index');
        Route::post('imagenes', [ImagenesProductoController::class, 'store'])->name('imagenes.store');
        Route::delete('imagenes/{imagen}', [ImagenesProductoController::class, 'destroy'])->name('imagenes.destroy');

        // Aquí luego podés agregar relacionados, variantes, etc.
        // Route::get('relacionados', [ProductosRelacionadosController::class, 'index'])->name('relacionados.index');
    });
});