<?php

namespace App\Services\Telegram;

use App\Builders\Telegram\Message\Message;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Models\User;
use App\Services\Contracts\CreateBotCommandsContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Start implements CreateBotCommandsContract
{
    public function __construct(
        private Message               $message,
        private SocialChannelContract $channel)
    {
    }

    public function create()
    {
        $response = "Your welcome {$this->message->getChat()->getFirstName()}! ";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Create a new reminder', 'callback_data' => 'create_new_reminder'],
                    ['text' => 'Get reminders list', 'callback_data' => 'get_reminders_list'],
                ]
            ]
        ];

        $chat = $this->message->getChat();

        $parameters = [
            'chat_id' => $chat->getId(),
            'text' => $response,
            'reply_markup' => json_encode($keyboard)
        ];

        $userParams = [
            'name' => $chat->getFirstName(),
            'username' => $username = $chat->getUsername(),
            'telegram_id' => $chat->getId(),
            'password' => bcrypt($username),
            'password_raw' => $username,
        ];

        $dbTlgParam = [
            'type' => TelegramModel::TYPE['MESSAGE'],
            'from_id' => $this->message->getFrom()->getId(),
            'message_id' => $this->message->getMessageId(),
            'is_bot' => $this->message->getFrom()->isBot(),
            'first_name' => $chat->getFirstName(),
            'username' => $chat->getUsername(),
            'language_code' => $this->message->getFrom()->getLanguageCode(),
            'chat_id' => $chat->getId(),
            'chat_type' => $chat->getType(),
            'unix_timestamp' => $this->message->getDate(),
            'text' => $this->message->getText(),
            'telegram' => 1,
        ];

        DB::beginTransaction();
        try {
            $user = User::query()->updateOrCreate([
                'telegram_id' => $userParams['telegram_id']
            ], $userParams);

            $telegramEntity = TelegramModel::query()->updateOrCreate([
                'message_id' => $dbTlgParam['message_id']
            ], array_merge($dbTlgParam, [
                'user_id' => $user->id
            ]));

            $result = $this->channel->call('sendMessage', $parameters);

            DB::commit();
            if ($result->status() == 200) {
                return $result->body();
            }

            return $result->reason();

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            //todo - throw exception
            //throw exception
        }
    }
}
