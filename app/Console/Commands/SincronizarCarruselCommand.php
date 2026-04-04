<?php

namespace App\Console\Commands;

use App\Services\CarruselSyncService;
use Illuminate\Console\Command;

class SincronizarCarruselCommand extends Command
{
    protected $signature = 'carrusel:sincronizar';

    protected $description = 'Sincroniza banners del carrusel según rango de fechas y activación manual';

    public function handle(): int
    {
        app(CarruselSyncService::class)->sincronizar(now());

        $this->info('Sincronización de carrusel ejecutada correctamente.');

        return self::SUCCESS;
    }
}