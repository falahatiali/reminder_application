<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\MessageDVO;
use App\Helpers\SocialChannelContract;
use App\Models\ReminderModel;
use App\Models\TelegramModel;
use App\Service\Contracts\CreateBotCommandsContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateAdditionalText implements CreateBotCommandsContract
{
    public function __construct(private MessageDVO $message, private SocialChannelContract $channel)
    {
    }

    public function create()
    {
        $response = "{$this->message->getChat()->getFirstName()}, Ok. choose the frequency 🤗";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Every Minute', 'callback_data' => 'everyMinute'],
                    ['text' => 'Every Two Minutes', 'callback_data' => 'everyTwoMinutes'],
                ],
                [
                    ['text' => 'Every Three Minutes', 'callback_data' => 'everyThreeMinutes'],
                    ['text' => 'Every Four Minutes', 'callback_data' => 'everyFourMinutes'],
                ],
                [
                    ['text' => 'Every Five Minutes', 'callback_data' => 'everyFiveMinutes'],
                    ['text' => 'Every Fifteen Minutes', 'callback_data' => 'everyFifteenMinutes'],
                ],
                [
                    ['text' => 'Every Thirty Minutes', 'callback_data' => 'everyThirtyMinutes'],
                    ['text' => 'Every Two Hours', 'callback_data' => 'everyTwoHours'],
                ],
                [
                    ['text' => 'Every Three Hours', 'callback_data' => 'everyThreeHours'],
                    ['text' => 'Every Four Hours', 'callback_data' => 'everyFourHours'],
                ],
                [
                    ['text' => 'Every Six Hours', 'callback_data' => 'everySixHours'],
                    ['text' => 'Every hour', 'callback_data' => 'hourly'],
                ],
                [
                    ['text' => 'Every day', 'callback_data' => 'daily'],
                    ['text' => 'Every week', 'callback_data' => 'weekly'],
                ],
                [
                    ['text' => 'Every month', 'callback_data' => 'monthly'],
                    ['text' => 'Every year', 'callback_data' => 'yearly'],
                ]
            ],
            'resize_keyboard' => true
        ];

        $parameters = [
            'chat_id' => $this->message->getChat()->getId(),
            'text' => $response,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $this->message->getMessageId(),
            'reply_markup' => json_encode($keyboard)
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['CALLBACK_QUERY'],
                'from_id' => $this->message->getFrom()->getId(),
                'message_id' => $this->message->getMessageId(),
                'is_bot' => $this->message->getFrom()->isBot(),
                'first_name' => $this->message->getChat()->getFirstName(),
                'username' => $this->message->getChat()->getUsername() ?? '',
                'language_code' => $this->message->getFrom()->getLanguageCode() ?? '',
                'chat_id' => $this->message->getChat()->getId(),
                'chat_type' => $this->message->getChat()->getType(),
                'unix_timestamp' => $this->message->getDate(),
                'text' => $this->message->getText(),
                'telegram' => json_encode($this->message->toArray(), true),
                'reminder_type' => 'additional_text',
                'user_id' => $this->message->getUserId()
            ];

            $backend = TelegramModel::query()->create($dbTlgParam);

            $reminder = ReminderModel::query()
                ->where('user_id', $dbTlgParam['user_id'])
                ->where('is_complete', false)
                ->update([
                    'additional_text' => $dbTlgParam['text']
                ]);

            DB::commit();

            return $this->channel->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            //todo
        }
    }
}
