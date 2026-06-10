<?php

namespace App\Services;

use App\Models\Producto;
use Carbon\Carbon;

class PromocionesSyncService
{
    public function sincronizar(?Carbon $ahora = null): void
    {
        $ahora = $ahora ?? now();

        Producto::query()
            ->where('descuento_activo', 1)
            ->whereNotNull('descuento_fin')
            ->where('descuento_fin', '<=', $ahora)
            ->update([
                'descuento_activo' => 0,
            ]);
    }
}