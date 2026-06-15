<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Configuracion;
use App\Models\Marca;
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
        View::composer('*', function ($view) {

        $configTienda = Configuracion::pluck('valor', 'clave')->toArray();

        $categoriasMenu = Categoria::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $marcasMenu = Marca::where('activo', 1)
            ->orderBy('nombre')
            ->get();

            $view->with([
                'configTienda' => $configTienda,
                'categoriasMenu' => $categoriasMenu,
                'marcasMenu' => $marcasMenu,
            ]);

        });
    }
}