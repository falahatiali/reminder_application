<?php

namespace App\Library\Reminder;

use App\Contracts\ReminderContract;
use App\Contracts\SendMessageContract;
use App\Models\ReminderModel;

class SendReminder implements ReminderContract
{
    private SendMessageContract $sendMessageContract;

    public ReminderModel $reminderModel;

    public function __construct(SendMessageContract $sendMessageContract, ReminderModel $reminder)
    {
        $this->sendMessageContract = $sendMessageContract;

        $this->reminderModel = $reminder;
    }

    public function sendReminder()
    {
        $this->sendMessageContract->send($this->reminderModel);
    }
}
