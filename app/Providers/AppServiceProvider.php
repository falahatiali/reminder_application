<?php

namespace App\Providers;

use App\Contracts\SendMessageContract;
use App\Library\Reminder\Channels\TelegramReminder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(SendMessageContract::class , TelegramReminder::class);
    }
}
