<?php

namespace App\Library\Reminder;

use App\Models\ReminderModel;
use Illuminate\Support\Facades\Http;

class TelegramReminder
{
    private ReminderModel $reminder;

    public function __construct(ReminderModel $reminder)
    {
        $this->reminder = $reminder;
    }

    private function buildRequestUrl(): string
    {
        $telegramToken = config('services.telegram-bot-api.token');
        $telegramChatId = config('services.telegram-bot-api.chatId');

        return "https://api.telegram.org/bot{$telegramToken}/sendMessage?chat_id={$telegramChatId}&text={$this->reminder->body}";
    }

    public function SendReminder()
    {
        return Http::get($this->buildRequestUrl())->body();
    }
}
