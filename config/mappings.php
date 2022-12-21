<?php

return [
    'list' => [
        'title' => 'reminders_list',
        'class' => \App\Services\Telegram\List\RemindersList::class
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
    ]
];
