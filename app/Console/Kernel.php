<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos disponibles en Artisan.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        // Si tienes un archivo routes/console.php, puedes cargarlo así:
        require base_path('routes/console.php');
    }

    /**
     * Define la programación de comandos automáticos (cron jobs).
     */
    protected function schedule(Schedule $schedule): void
    {
        //
    }
}
