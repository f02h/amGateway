<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

use Illuminate\Support\Facades\Crypt;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Cred;
use App\Register\Arnes;
use App\Register\Eurid;
use App\Register\Joker;



class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            foreach (Cred::all() as $gatewayCred) {
                $gateway = null;
                $conf = $gatewayCred->toArray();
                $conf['password'] = Crypt::decrypt($conf['password']);
                if ($gatewayCred->idGateway == 'Eurid') {
                    $gateway = new Eurid($conf);
                }
//                else if ($gatewayCred->idGateway == 'ArnesKlaro') {
//                    $gateway = new Arnes($conf);
//                } else if ($gatewayCred->idGateway == 'JokerDMV') {
//                    $gateway = new Joker($conf);
//                } else if ($gatewayCred->idGateway == 'Carnet') {
//                    $gateway = new Carnet($conf);
//                }

                if (!is_null($gateway)) {
                    $gateway->readMessages();
                }
            }
        })->everyFiveMinutes();

    }
}
