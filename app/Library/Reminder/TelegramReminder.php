<?php

namespace App\Library\Reminder;

use App\Contracts\SendMessageContact;
use App\Models\ReminderModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramReminder implements SendMessageContact
{
    private ReminderModel $reminder;

    public function __construct(ReminderModel $reminder)
    {
        $this->reminder = $reminder;
    }

    private function buildRequestUrl(): string
    {
        $url = "https://api.telegram.org/bot{$this->getTelegramToken()}/sendMessage?";
        $url .= "chat_id={$this->getTelegramChatId()}";
        $url .= "&parse_mode=HTML";
        $url .= "&text={$this->buildFormattedText()}";

        Log::error($url);
        return $url;
    }

    private function getTelegramToken()
    {
        return config('services.telegram-bot-api.token');
    }

    private function getTelegramChatId()
    {
        return config('services.telegram-bot-api.chatId');
    }

    public function SendReminder()
    {
        return $this->send($this->buildRequestUrl());
    }

    public function send($message)
    {
        return Http::get($message)->body();
    }

    private function buildFormattedText()
    {
        $text = "<b>{$this->reminder->frontend}</b>, <strong>{$this->reminder->frontend}</strong>\n\n\n";
        $text .= "<b><span class=\"tg-spoiler\">{$this->reminder->backend}</span></b>\n\n\n";

        if (Str::length($this->reminder->body) > 0) {
            $text .= "<b><i>TEXT BODY</i></b>\n";
            $text .= "<pre>{$this->reminder->body}</pre>\n\n\n";
        }

        if (Str::length($this->reminder->additional_text) > 0) {
            $text .= "<b><i>Additional Text</i></b>\n";
            $text .= "<pre><code class=\"language-python\">{$this->reminder->additional_text}</code></pre>\n\n\n";
        }

        return $text;
    }
}
