<?php

namespace App\Console\Commands;

use App\Services\PromocionesSyncService;
use Illuminate\Console\Command;

class SincronizarPromocionesCommand extends Command
{
    protected $signature = 'promociones:sincronizar';

    protected $description = 'Sincroniza promociones vencidas';

    public function handle(): int
    {
        app(PromocionesSyncService::class)->sincronizar(now());

        $this->info('Sincronización de promociones ejecutada correctamente.');

        return self::SUCCESS;
    }
}