<?php

namespace App\Console;

use App\Jobs\SendOrderDurationOverdrawn;
use App\Jobs\SendOrderReminder;
use App\Jobs\SendOrderReminderTeacher;
use App\Mail\OrderReminder;
use App\Order;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //remove from shoppingCart after one hour
        $schedule->call(function () {
            DB::table('device_user')->where('created_at', '<=', Carbon::now()->subMinutes(Setting::all()->find('removeFromShoppingCartAfter')->value))->delete();
        })->everyMinute();

        //entferne von benutzer ausgewählte tage, damit keine falschen daten zustande kommen
        $schedule->call(function () {
            $users = User::where('from', '<', Carbon::today())
                ->orWhere('to', '<', Carbon::today())->get();

            foreach ($users as $user) {
                $user->from = null;
                $user->to = null;
                $user->save();

                //auch bereits ausgewählte Geräte aus Warenkorb löschen
                $user->clearShoppingCart();
            }

            Log::info('Von ' . $users->count() . ' Benutzern vorausgewaehlte Tage geloescht.');
        })->dailyAt('00:01');

        //email, wenn man zurückgeben muss
        $schedule->call(function () {
            //alle Bestellungen, die in x Tagen enden
            $orders = Order::where([
                ['to', '=', Carbon::today()->addDays(Setting::all()->find('sendEmailBefore')->value)],
            ])->get();

            foreach ($orders as $order) {
                //der status wenn man die Geräte zurzeit hat, ist 3.
                if($order->status()[0] === 3)
                {
                    SendOrderReminder::dispatch($order)->onConnection('database');
                    SendOrderReminderTeacher::dispatch($order)->onConnection('database');
                    Log::info('Erinnerungsmail: ' . $order->user->displayName() . ' - OrderID: ' . $order->id);
                }
            }
        })->dailyAt('18:00');

        //email an Teacher, wenn bestellung heute nicht zurückgegeben wurde..
        $schedule->call(function () {
            //alle Bestellungen, die gestern enden hätten sollen
            $orders = Order::where([
                ['to', '=', Carbon::today()->subDay()],
            ])->get();

            foreach ($orders as $order) {
                //der status, wenn man nicht zurückgegeben hat, ist 3.
                if($order->status()[0] === 3)
                {
                    SendOrderDurationOverdrawn::dispatch($order)->onConnection('database');
                    Log::info('Ueberziehungsmail: ' . $order->user->displayName() . ' - OrderID: ' . $order->id);
                }
            }
        })->dailyAt('17:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
