<?php

use Illuminate\Support\Facades\Route;

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

// ✅ Redirigir raíz al dashboard admin (opcional)
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// ✅ Rutas admin
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD principales
    Route::resource('carrusel-items', CarruselItemsController::class);
    Route::resource('categorias', CategoriasController::class);
    Route::resource('cupones', CuponesController::class);
Route::resource('inventario-movimientos', MovimientosInventarioController::class)
    ->only(['index', 'create', 'store', 'show']); // ajustá si ocupás más
    Route::resource('marcas', MarcasController::class);
    Route::resource('productos', ProductosController::class);
    Route::resource('usuarios', UsuariosController::class);
    Route::resource('roles', RolesController::class); 
        Route::resource('configuracion', ConfiguracionController::class);

    // Pedidos (online)
Route::resource('pedidos', PedidosController::class)->only(['index', 'show']);

Route::get('pedidos/{id}/verificar', [PedidosController::class, 'verificar'])
    ->name('pedidos.verificar');

Route::patch('pedidos/{id}/aprobar-pago', [PedidosController::class, 'aprobarPago'])
    ->name('pedidos.aprobar-pago');

Route::patch('pedidos/{id}/rechazar-pago', [PedidosController::class, 'rechazarPago'])
    ->name('pedidos.rechazar-pago');

Route::patch('pedidos/{id}/actualizar-estado', [PedidosController::class, 'actualizarEstado'])
    ->name('pedidos.actualizar-estado');







    
    // Pagos pedidos y locales (verificar/rechazar normalmente es update)
    Route::resource('pagos-pedidos', PagosPedidosController::class)->only(['index', 'show', 'update']);




    // Ventas físicas
    Route::resource('ventas-locales', VentasLocalesController::class);
   Route::resource('pagos-ventas-locales', PagosVentasLocalesController::class)->only(['index','show']);


   
 //  Zonas de envío
    Route::resource('zonas-envio', ZonasEnvioController::class);

    //  Carga dinámica de ubicación
    Route::get('zonas-envio/cantones/{id_provincia}', [ZonasEnvioController::class, 'obtenerCantones'])
        ->name('zonas-envio.cantones');

    Route::get('zonas-envio/distritos/{id_canton}', [ZonasEnvioController::class, 'obtenerDistritos'])
        ->name('zonas-envio.distritos');

   

    // Reportes opcionales (pro)
    Route::resource('ventas', VentasController::class)->only(['index']);
Route::resource('usos-cupones', UsosCuponesController::class)->only(['index', 'show']);

    // Extras de producto (sub-recursos / acciones)
    Route::prefix('productos/{producto}')->name('productos.')->group(function () {
        // Imágenes del producto
        Route::get('imagenes', [ImagenesProductoController::class, 'index'])->name('imagenes.index');
        Route::post('imagenes', [ImagenesProductoController::class, 'store'])->name('imagenes.store');
        Route::delete('imagenes/{imagen}', [ImagenesProductoController::class, 'destroy'])->name('imagenes.destroy');

       
    });
    

});
