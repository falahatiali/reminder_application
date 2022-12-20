<?php

namespace App\Library\Reminder\Channels;

use App\Contracts\SendMessageContract;
use App\Models\ReminderModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramReminder implements SendMessageContract
{
    public function send(ReminderModel $reminderModel)
    {
        $url = $this->buildRequestUrl($reminderModel);
        return Http::get($url)->body();
    }

    private function buildRequestUrl(ReminderModel $reminderModel): string
    {
        $url = "https://api.telegram.org/bot{$this->getTelegramToken()}/sendMessage?";
        $url .= "chat_id={$this->getTelegramChatId($reminderModel)}";
        $url .= "&parse_mode=HTML";
        $url .= "&text={$this->buildFormattedText($reminderModel)}";

        return $url;
    }

    private function buildFormattedText(ReminderModel $reminder)
    {
        $text = "<b>{$reminder->frontend}</b>\n\n\n";
        $text .= "<b class='tg-spoiler'><span class='tg-spoiler'>{$reminder->backend}</span></b>\n\n\n";

        if (Str::length($reminder->body) > 0) {
            $text .= "<b><i>*******Text Body *******</i></b>\n";
            $text .= "<pre>{$reminder->body}</pre>\n\n\n";
        }

        if (Str::length($reminder->additional_text) > 0) {
            $text .= "<b><i>Additional Text</i></b>\n";
            $text .= "<pre><code class=\"language-python\">{$reminder->additional_text}</code></pre>\n\n\n";
        }

        return $text;
    }

    private function getTelegramToken()
    {
        return config('services.telegram.token');
    }

    private function getTelegramChatId(ReminderModel $reminder)
    {
        return $reminder->user->telegram_id;
    }
}
