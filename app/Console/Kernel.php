<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
//         $schedule->command('inspire')->hourly();
        $schedule->call(function(){
            $data = json_decode(Storage::disk('local')->get('access_token.json'),JSON_OBJECT_AS_ARRAY);
            $response = Http::withHeaders([
                'Content-Type'=> 'application/json'
            ])->post('https://'.env('AMOCRM_SUBDOMAIN').'.amocrm.ru/oauth2/access_token',
                [
                  "client_id"=> env('AMOCRM_INTEGRATION_ID'),
                  "client_secret"=> env('AMOCRM_APIKEY'),
                  "grant_type"=> "refresh_token",
                  "refresh_token"=> $data['refresh_token'],
                  "redirect_uri"=> env('AMOCRM_REDIRECT_URL')
                ]);
            Storage::disk('local')->put('access_token.json',$response);
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
