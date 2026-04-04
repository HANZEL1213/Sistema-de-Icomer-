<?php

namespace App\Services;

use App\Models\CarruselItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CarruselSyncService
{
    public function sincronizar(?Carbon $ahora = null): void
    {
        $ahora = $ahora ?? now();

        DB::transaction(function () use ($ahora) {
            $items = CarruselItem::query()
                ->lockForUpdate()
                ->orderBy('orden_programado')
                ->orderBy('id_carrusel_item')
                ->get();

            $ordenActual = 1;

            foreach ($items as $item) {
                $debeEstarActivo = $this->debeEstarActivo($item, $ahora);
                $nuevoOrden = $debeEstarActivo ? $ordenActual : 0;
                $nuevoActivo = $debeEstarActivo ? 1 : 0;

                $huboCambios =
                    (int) $item->activo !== $nuevoActivo ||
                    (int) $item->orden !== $nuevoOrden;

                if ($huboCambios) {
                    $item->update([
                        'activo' => $nuevoActivo,
                        'orden' => $nuevoOrden,
                    ]);
                }

                if ($debeEstarActivo) {
                    $ordenActual++;
                }
            }
        });
    }

    private function debeEstarActivo(CarruselItem $item, Carbon $ahora): bool
    {
        return (bool) $item->activo_manual
            && $item->inicia_en
            && $item->termina_en
            && $item->inicia_en->lte($ahora)
            && $item->termina_en->gt($ahora);
    }
}