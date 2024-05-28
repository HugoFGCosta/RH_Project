<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // $schedule->command('inspire')->hourly();
        //$schedule->command('check:presences')->everyMinute(); // CORRE a verificaçao cada 1 minuto
        $schedule->command('check:presences')->everyTenMinutes(); // CORRE a verificaçao cada 10 minutos
        //$schedule->command('check:presences')->everyThirtyMinutes(); // CORRE a verificaçao cada 30 minutos

        //Verificações de faltas Primeiro e Segundo Turno
        $schedule->call('App\Http\Controllers\AbsenceController@verifyFirstShiftAbsence')->everyMinute();
        $schedule->call('App\Http\Controllers\AbsenceController@verifySecondShiftAbsence')->everyMinute();

    }




    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
