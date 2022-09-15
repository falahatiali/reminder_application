<?php

namespace App\Helpers\BotComponents;

use App\Helpers\Telegram;
use App\Models\TelegramModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class Start implements TelegramComponentContract
{
    private array $data;


    public function __construct(array $data)
    {
//        $data = [
//            "update_id": 460984372,
//            "message": {
        //            "message_id": 12,
        //                "from": {
        //                    "id": 1977093554,
        //                            "is_bot": false,
        //                            "first_name": "Fala",
        //                            "username": "alifala99",
        //                            "language_code": "en"
        //                        },
        //                "chat": {
        //                    "id": 1977093554,
        //                            "first_name": "Fala",
        //                            "username": "alifala99",
        //                            "type": "private"
        //                        },
        //                "date": 1662976667,
        //                "text": "/start",
        //                "entities": [
        //                    {
        //                        "offset": 0,
        //                        "length": 6,
        //                        "type": "bot_command"
        //                    }
        //                ]
//            }
//        ]
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
            'password' => $username,
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
            DB::rollBack();
            //todo - throw exception
            //throw exception
        }
    }
}
