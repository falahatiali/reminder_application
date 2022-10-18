<?php

namespace App\Helpers\BotComponents;

use App\Helpers\Telegram;
use App\Models\TelegramModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Start implements TelegramComponentContract
{
    private array $data;


    public function __construct(array $data)
    {
        $this->data = $data['message'];
    }

    public function run()
    {
        $response = "Your welcome {$this->data['chat']['first_name']}!";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Create a new reminder', 'callback_data' => 'create_new_reminder'],
                    ['text' => 'Get reminders list', 'callback_data' => 'get_reminders_list'],
                ]
            ]
        ];

        $parameters = [
            'chat_id' => $this->data['chat']['id'],
            'text' => $response,
            'reply_markup' => json_encode($keyboard)
        ];

        $userParams = [
            'name' => $this->data['chat']['first_name'],
            'username' => $username = $this->data['chat']['username'],
            'telegram_id' => $this->data['chat']['id'],
            'password' => bcrypt($username),
            'password_raw' => $username,
        ];

        $dbTlgParam = [
            'type' => TelegramModel::TYPE['MESSAGE'],
            'from_id' => $this->data['from']['id'],
            'message_id' => $this->data['message_id'],
            'is_bot' => $this->data['from']['is_bot'],
            'first_name' => $this->data['chat']['first_name'],
            'username' => $this->data['chat']['username'] ?? '',
            'language_code' => $this->data['from']['language_code'],
            'chat_id' => $this->data['chat']['id'],
            'chat_type' => $this->data['chat']['type'],
            'unix_timestamp' => $this->data['date'],
            'text' => $this->data['text'],
            'telegram' => $this->data,
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

            $telegram = app(Telegram::class);
            $result = $telegram->call('sendMessage', $parameters);

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
