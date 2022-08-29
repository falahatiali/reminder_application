<?php

namespace App\Contracts;

use App\Models\ReminderModel;
use App\Models\User;

interface ReminderContract
{
    public function SendReminder();
}
