<?php

namespace App\Console;

use App\Library\Reminder\Channels\TelegramReminder;
use App\Library\Reminder\SendReminder;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        Log::error("****************** Start cron job processing ************************ ");
        User::query()->with('reminders')->each(function ($user) use ($schedule) {
            $user->reminders()->active()->each(function ($reminder) use ($user, $schedule) {
                return $schedule->call(function () use ($reminder) {
                    $sendReminder = app(SendReminder::class , ['reminder' => $reminder]);
                    $sendReminder->sendReminder();
//                    $reminderAgent = app(TelegramReminder::class, ['reminder' => $reminder]);
//                    $reminderAgent->SendReminder();
                })->cron($reminder->expression);
            });
        });
        Log::error("****************** END ************************ ");
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
