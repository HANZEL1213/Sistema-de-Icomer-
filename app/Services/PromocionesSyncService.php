<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoVariante;
use Carbon\Carbon;

class PromocionesSyncService
{
    public function sincronizar(?Carbon $ahora = null): void
    {
        $ahora = $ahora ?? now();

        // Productos normales
        Producto::query()
            ->where('descuento_activo', 1)
            ->whereNotNull('descuento_fin')
            ->where('descuento_fin', '<=', $ahora)
            ->update([
                'descuento_activo' => 0,
            ]);

        // Variantes
        ProductoVariante::query()
            ->where('descuento_activo', 1)
            ->whereNotNull('descuento_fin')
            ->where('descuento_fin', '<=', $ahora)
            ->update([
                'descuento_activo' => 0,
            ]);
    }
}