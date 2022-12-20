<?php

namespace App\Services\Telegram\List;

use App\Builders\Telegram\Message\Message;
use App\Helpers\SocialChannelContract;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\ListBotCommandsContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RemindersList implements ListBotCommandsContract
{
    public function __construct(
        private Message                     $message,
        private ReminderRepositoryInterface $reminderRepository,
        private UserRepositoryInterface     $userRepository,
        private SocialChannelContract       $channel
    )
    {
    }

    public function action(): object
    {
        $user = $this->userRepository
            ->findWhere('telegram_id', '=', $this->message->getChat()->getId())
            ->first();

        if ($user != null) {
            /** @var Collection $reminders */
            $reminders = collect($this->reminderRepository
                ->findWhere('user_id', '=', $user->id)
                ->get());

            $response = "{$this->message->getChat()->getFirstName()}, Your reminders list: ";
            $inlines = [];

            foreach ($reminders as $reminder) {
                $inlines[] = [
                    'text' => $reminder->frontend,
                    'callback_data' => $reminder->id . '_' . $reminder->frontend
                ];
            }

            $keyboard = [
                'inline_keyboard' => [
                    $inlines
                ],
                'resize_keyboard' => true
            ];

            $parameters = [
                'chat_id' => $this->message->getChat()->getId(),
                'text' => $response,
                'parse_mode' => 'HTML',
                'entities' => json_encode([
                    [
                        'type' => '#list',
                        'offset' => 1,
                        'length' => 10
                    ],
                    [
                        'type' => '#reminders',
                        'offset' => 2,
                        'length' => 10
                    ],
                ]),
                'reply_to_message_id' => $this->message->getMessageId(),
                'reply_markup' => json_encode($keyboard)
            ];

            return $this->channel->call('sendMessage', $parameters);
        }

        return $this->channel->call('sendMessage', [
            'chat_id' => $this->message->getChat()->getId(),
            'text' => "There is not any reminders yet",
            'reply_to_message_id' => $this->message->getMessageId(),
        ]);
    }
}
