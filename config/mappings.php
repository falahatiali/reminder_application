<?php

return [
    'list' => [
        'title' => 'reminders_list',
        'class' => \App\Services\Telegram\List\RemindersList::class
    ],
    'create' => [
        'title' => 'start',
        'class' => \App\Services\Telegram\Start::class
    ],
    'start' => [
        'title' => 'start',
        'class' => \App\Services\Telegram\Start::class
    ],
    'deleteall' => [
        'title' => 'deleteAll',
        'class' => \App\Services\Telegram\Delete\DeleteAll::class
    ],
    'delete_reminder' => [
        'title' => 'delete_reminder',
        'class' => \App\Services\Telegram\Delete\DeleteReminder::class
    ],
    'create_new_reminder' => [
        'title' => 'rew_reminder',
        'class' => \App\Services\Telegram\Create\NewReminder::class
    ],
    'daily' => [
        'title' => 'daily',
        'class' => \App\Services\Telegram\Create\FrequencyDaily::class
    ],
    'weekly' => [
        'title' => 'weekly',
        'class' => \App\Services\Telegram\Create\FrequencyWeekly::class
    ],
    'monthly' => [
        'title' => 'monthly',
        'class' => \App\Services\Telegram\Create\FrequencyMonthly::class
    ],
    'yearly' => [
        'title' => 'yearly',
        'class' => \App\Services\Telegram\Create\FrequencyYearly::class
    ],
    'hour_*' => [
        'title' => 'choose_hour',
        'class' => \App\Services\Telegram\Create\FrequencySelectHour::class
    ],
    'minute_*' => [
        'title' => 'choose_minute',
        'class' => \App\Services\Telegram\Create\FrequencySelectMinute::class
    ],
    'day_*' => [
        'title' => 'choose_day',
        'class' => \App\Services\Telegram\Create\FrequencyDaily::class
    ],
    'day_in_month_*' => [
        'title' => 'choose_day_in_month',
        'class' => \App\Services\Telegram\Create\FrequencyDaily::class
    ],
    'month_*' => [
        'title' => 'month',
        'class' => \App\Services\Telegram\Create\FrequencyMonthly::class
    ]
];
