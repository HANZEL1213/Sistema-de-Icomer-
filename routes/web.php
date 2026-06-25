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
    CuentaController,
    FavoritoController,
    PedidoController,
    PagoPedidoController,
    PasswordResetController,
    TiendaAuthController,
};

/*
|--------------------------------------------------------------------------
| CONTROLLERS - ADMIN
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\{
    AuthController,
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
    PerfilController,
    ZonasEnvioController,
    VentasController,
    UsosCuponesController
};

/*
|--------------------------------------------------------------------------
| AUTH TIENDA
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->name('tienda.auth.')->group(function () {

    Route::get('/login', [TiendaAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [TiendaAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    Route::get('/registro', [TiendaAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/registro', [TiendaAuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.post');

    // GOOGLE
    Route::get('/google', [TiendaAuthController::class, 'redirectToGoogle'])
        ->name('google.redirect');

    Route::get('/google/callback', [TiendaAuthController::class, 'handleGoogleCallback'])
        ->name('google.callback');

    // VERIFICAR CORREO
    Route::get('/email/verificar/{token}', [TiendaAuthController::class, 'verifyEmail'])
        ->name('email.verify');

    Route::get('/email/reenviar', function () {
        return redirect()->route('tienda.auth.login')
        ->with('swal_error', 'Debes usar el formulario para reenviar el correo de verificación.');
    });
    Route::post('/email/reenviar', [TiendaAuthController::class, 'resendVerification'])
        ->middleware('throttle:2,1')
        ->name('email.resend');

    // Route::get('/logout', function () {
    // return redirect()
    //     ->route('tienda.auth.login')
    //     ->with('swal_error', 'Usa el botón de cerrar sesión.');
    // })->name('logout.redirect');

    // Route::post('/logout', [TiendaAuthController::class, 'logout'])
    //     ->name('logout');

     Route::get('/password/forgot', [PasswordResetController::class, 'showForgot'])
        ->name('password.forgot');

    Route::post('/password/email', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:5,10')
        ->name('password.email');

    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showReset'])
        ->name('password.reset');

    Route::post('/password/reset', [PasswordResetController::class, 'reset'])
        ->name('password.update');
});


/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS - TIENDA
|--------------------------------------------------------------------------
*/

Route::prefix('tienda')->name('tienda.')->middleware(['cliente'])->group(function () {

    Route::get('/mi-cuenta', [CuentaController::class, 'index'])->name('cuenta');

    Route::put('/mi-cuenta', [CuentaController::class, 'update'])
        ->name('cuenta.update');

    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('pedidos.mis');

    Route::put('/mi-cuenta/password', [CuentaController::class, 'updatePassword'])
        ->name('cuenta.password');
    Route::get('/mi-cuenta/password', function () {
        return redirect()->route('tienda.cuenta');
    });

    Route::get('/logout', function () {
        return redirect()
            ->route('tienda.auth.login')
            ->with('swal_error', 'Usa el botón de cerrar sesión.');
    })->name('logout.redirect');
    
    Route::post('/logout', [TiendaAuthController::class, 'logout'])
        ->name('logout');
});


/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS - TIENDA
|--------------------------------------------------------------------------
| Aquí va toda la navegación del cliente:
| home, catálogo, categorías, marcas, carrito, checkout, pedidos y seguimiento.
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
| AJAX PRODUCTOS HOME
|--------------------------------------------------------------------------
*/
Route::get(
    '/home/productos',
    [HomeController::class, 'productosAjax']
)->name('home.productos.ajax');



    /*
        |--------------------------------------------------------------------------
        | FAVORITOS
        |--------------------------------------------------------------------------
        */

    Route::prefix('favoritos')
        ->name('favoritos.')
        ->group(function () {

            Route::get(
                '/',
                [FavoritoController::class, 'index']
            )->name('index');

            Route::post(
                '/toggle/{id}',
                [FavoritoController::class, 'toggle']
            )->name('toggle');
        });



    /*
    |--------------------------------------------------------------------------
    | Productos
    |--------------------------------------------------------------------------
    */
    Route::prefix('productos')
        ->name('productos.')
        ->group(function () {

            Route::get('/', [ProductoController::class, 'index'])
                ->name('index');

            // 🔍 SUGERENCIAS
            Route::get('/buscar/sugerencias', [ProductoController::class, 'sugerencias'])
                ->name('sugerencias');

            Route::get('/{slug}', [ProductoController::class, 'show'])
                ->name('show');
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

    Route::prefix('carrito')
        ->name('carrito.')
        ->controller(CarritoController::class)
        ->group(function () {

            Route::get('/', 'index')->name('index');

            Route::post('/agregar/{producto}', 'agregar')
            ->middleware('throttle:30,1')
            ->name('agregar');

            Route::patch('/actualizar/{producto}', 'actualizar')->name('actualizar');

            Route::delete('/eliminar/{producto}', 'eliminar')->name('eliminar');

            Route::delete('/vaciar', 'vaciar')->name('vaciar');

            /*
        |--------------------------------------------------------------------------
        | CUPONES
        |--------------------------------------------------------------------------
        */

            Route::post('/cupon/aplicar', 'aplicarCupon')
                ->middleware('throttle:10,1')
                ->name('cupon.aplicar');

            Route::delete('/cupon/eliminar', 'eliminarCupon')
                ->name('cupon.eliminar');
        });

    /*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/
    Route::prefix('checkout')->name('checkout.')->group(function () {

        Route::get('/', [CheckoutController::class, 'index'])
            ->name('index');

        Route::post('/confirmar', [CheckoutController::class, 'confirmar'])
            ->middleware('throttle:5,1')
            ->name('confirmar');

        Route::get('/cantones/{id_provincia}', [CheckoutController::class, 'cantonesDisponibles'])
            ->name('cantones');

        Route::get('/distritos/{id_canton}', [CheckoutController::class, 'distritosDisponibles'])
            ->name('distritos');

        Route::get('/confirmacion/{pedido}', [CheckoutController::class, 'confirmacion'])
            ->name('confirmacion');

        Route::get('/costo-envio/{id_distrito}', [CheckoutController::class, 'costoEnvio'])
            ->name('costo.envio');
    });

    /*
    |--------------------------------------------------------------------------
    | TERMINOS Y CONDICIONES
    |--------------------------------------------------------------------------
    */
    Route::prefix('terminos')->name('terminos.')->group(function () {

        Route::view('/cambios-y-devoluciones', 'tienda.politicas.terminos_condiciones')
            ->name('condiciones');
    });

    /*
    |--------------------------------------------------------------------------
    | Pedidos del cliente
    |--------------------------------------------------------------------------
    */
    Route::prefix('pedido')->name('pedidos.')->group(function () {
        // Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('mis');
        Route::get('{codigo}', [PedidoController::class, 'show'])->name('show');
        Route::get('{codigo}/seguimiento', [PedidoController::class, 'seguimiento'])->name('seguimiento');

        Route::get('/tienda/rastrear-pedido', [PedidoController::class, 'rastrear'])
            ->name('rastrear');

        Route::post('/tienda/rastrear-pedido', [PedidoController::class, 'buscarSeguimiento'])
            ->name('buscarSeguimiento');
    });

    /*
|--------------------------------------------------------------------------
| Pago de pedido
|--------------------------------------------------------------------------
*/
    Route::prefix('pedido')->name('pagos.')->group(function () {

        /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE PAGO
    |--------------------------------------------------------------------------
    */
        Route::get(
            '{codigo}/pago',
            [PagoPedidoController::class, 'index']
        )->name('index');

        /*
    |--------------------------------------------------------------------------
    | ENVIAR / REENVIAR PAGO
    |--------------------------------------------------------------------------
    */
        Route::post(
            '{codigo}/pago',
            [PagoPedidoController::class, 'store']
        )->middleware('throttle:5,1')->name('store');
    });

    /*
|--------------------------------------------------------------------------
| CIERRE RUTAS TIENDA
|--------------------------------------------------------------------------
*/
});



/*
|--------------------------------------------------------------------------
| LOGIN ADMIN (PÚBLICO)
|--------------------------------------------------------------------------
*/

// Route::get('/login', function () {
//     return redirect()->route('admin.login');
// })->name('login');

Route::prefix('panel-adminjh')->name('admin.')->group(function () {

    Route::get('/acceso', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/acceso', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS - ADMIN
|--------------------------------------------------------------------------
| Aquí va toda la gestión administrativa del sistema:
| dashboard, CRUDs, pedidos, ventas, inventario, usuarios, etc.
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    
    Route::get('/logout', function () {
        return redirect()
            ->route('admin.login')
            ->with('error', 'Usa el botón de cerrar sesión.');
    })->name('logout.redirect');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Perfil ADMIN
    |--------------------------------------------------------------------------
    */
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');

    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    Route::get('/perfil/password', function () {
        return redirect()
            ->route('admin.perfil')
            ->with('error', 'Para actualizar tu contraseña, usa el formulario del perfil.');
    })->name('perfil.password.redirect');
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

Route::get('ventas-locales/{id}/ticket', [VentasLocalesController::class, 'ticket'])
    ->name('ventas-locales.ticket');

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
