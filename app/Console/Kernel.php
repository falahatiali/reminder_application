<?php

namespace App\Console;

use App\Library\Reminder\TelegramReminder;
use App\Models\User;
use App\Scheduler\SendReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        User::query()->with('reminders')->each(function ($user) use ($schedule) {
            $user->reminders()->each(function ($reminder) use ($user, $schedule) {
                return $schedule->call(function () use ($reminder) {
                    $reminderAgent = app(TelegramReminder::class , ['reminder' => $reminder]);
                    $reminderAgent->SendReminder();
                })->cron($reminder->expression);
            });
        });
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
