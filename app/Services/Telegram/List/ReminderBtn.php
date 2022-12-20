<?php

namespace App\Services\Telegram\List;

use App\Builders\Telegram\Query\Query;
use App\Services\Contracts\ListBotCommandsContract;

class ReminderBtn implements ListBotCommandsContract
{
    public function __construct(private Query $query)
    {
    }

    public function action()
    {
        // TODO: Implement action() method.
    }
}
