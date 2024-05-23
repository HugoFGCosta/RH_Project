<?php

namespace App\Console\Commands;

use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPresences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:check-presences';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    protected $signature = 'check:presences';
    protected $description = 'Check for presences that have not been ended after 15 hours';

    public function handle()
    {
        // METODO das 15 horas auto-picagem
        $presences = Presence::where('first_start', '<=', Carbon::now()->subHours(15))
            ->whereNull('first_end')
            ->get();

        foreach ($presences as $presence) {
            $presence->first_end = $presence->first_start->addHours(15);
            $presence->second_start = $presence->first_end;
            $presence->second_end = $presence->second_start->addHours(15);
            $presence->save();
        }

        $presences = Presence::where('first_end', '<=', Carbon::now()->subHours(15))
            ->whereNotNull('second_start')
            ->whereNull('second_end')
            ->get();

        foreach ($presences as $presence) {
            $presence->second_end = $presence->first_start->addHours(15);
            $presence->save();
        }
    }


    /* public function handle()
    {
        // metodo de 1 minuto
        $presences = Presence::where('first_start', '<=', Carbon::now()->subMinute())
            ->whereNull('first_end')
            ->get();

        foreach ($presences as $presence) {
            $presence->first_end = Carbon::now();
            $presence->save();
        }

        $presences = Presence::where('first_start', '<=', Carbon::now()->subMinute())
            ->whereNull('second_end')
            ->get();

        foreach ($presences as $presence) {
            $presence->second_end = Carbon::now();
            $presence->save();
        }
    } */
}