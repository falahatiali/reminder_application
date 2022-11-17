<?php

namespace App\Providers;

use App\Contracts\SendMessageContract;
use App\Helpers\SocialChannelContract;
use App\Helpers\Telegram;
use App\Library\Reminder\Channels\TelegramReminder;
use App\Service\BotCommands\Create\CreateFront;
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
        $this->app->singleton(SendMessageContract::class, TelegramReminder::class);

        $this->app->singleton(SocialChannelContract::class, Telegram::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
