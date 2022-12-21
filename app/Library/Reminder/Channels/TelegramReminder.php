<?php

namespace App\Library\Reminder\Channels;

use App\Contracts\SendMessageContract;
use App\Helpers\SocialChannelContract;
use App\Models\ReminderModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramReminder implements SendMessageContract
{
    public function __construct(private SocialChannelContract $channel)
    {
    }

    public function send(ReminderModel $reminderModel): object
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Delete', 'callback_data' => 'delete_reminder'],
                    ['text' => 'Edit', 'callback_data' => 'edit_reminder'],
                ]
            ]
        ];

        $parameters = [
            'chat_id' => $reminderModel->user->telegram_id,
            'text' => $this->buildFormattedText($reminderModel),
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ];

        return $this->channel->call('sendMessage', $parameters);
    }

    private function buildFormattedText(ReminderModel $reminder): string
    {
        $text = "<b>{$reminder->frontend} </b>\n\n\n";
        $text .= "<b class='tg-spoiler'><span class='tg-spoiler'>{$reminder->backend}</span></b>\n\n\n";

        if (Str::length($reminder->body) > 0) {
            $text .= "<b><i>******* Text Body *******</i></b>\n";
            $text .= "<pre>{$reminder->body}</pre>\n\n\n";
        }

        if (Str::length($reminder->additional_text) > 0) {
            $text .= "<b><i>******* Additional Text *******</i></b>\n";
            $text .= "<pre><code class=\"language-python\">{$reminder->additional_text}</code></pre>\n\n\n";
        }

        return $text;
    }
}
