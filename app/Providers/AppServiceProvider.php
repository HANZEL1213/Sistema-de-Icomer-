<?php

namespace App\Providers;

use App\Models\Configuracion;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('tienda.*', function ($view) {

            $configTienda = Configuracion::pluck('valor', 'clave')->toArray();

            $view->with('configTienda', $configTienda);

        });
    }
}