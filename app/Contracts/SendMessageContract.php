<?php

namespace App\Contracts;

use App\Models\ReminderModel;

interface SendMessageContract
{
    public function send(ReminderModel $reminderModel);
}
