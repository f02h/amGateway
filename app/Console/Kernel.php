<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Crypt;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Cred;

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
        //

        $schedule->call(function () {
            foreach (Cred::all() as $gateway) {
                $epp = null;
//                if ($gateway->idGateway == 'Arnes') {
//                    $epp = new \App\Register\Arnes($gateway->username, Crypt::decrypt($gateway->password), $gateway->transport, $gateway->hostname, $gateway->port);
//                }

                if ($gateway->idGateway == 'Eurid') {
                    $epp = new \App\Register\Eurid($gateway->username, Crypt::decrypt($gateway->password), $gateway->transport, $gateway->hostname, $gateway->port);
                }

                if ($epp) {
                    $epp->readMessages();
                }
            }
        })->everyMinute();

    }
}
