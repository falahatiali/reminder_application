<?php

namespace App\Http\Controllers;

use App\Builders\Telegram\Chat\ChatBuilder;
use App\Models\TelegramModel;
use App\Repositories\Contracts\TelegramRepositoryInterface;

class TestController extends Controller
{
    public function test()
    {
        $dbTlgParam = [
            'type' => TelegramModel::TYPE['MESSAGE'],
            'from_id' => 12323213,
            'message_id' => 1232132,
            'is_bot' => false,
            'first_name' => '123331ad' . time(),
            'username' => '123user' . time(),
            'language_code' => 'en',
            'chat_id' => time(),
            'chat_type' => 'normal',
            'unix_timestamp' => time(),
            'text' => time() . 'test' . time(),
            'telegram' => ['key1' => 1, 'key2' => 2],
            'reminder_type' => 'backend',
            'user_id' => 1
        ];
        $telegram = app(TelegramRepositoryInterface::class);
        $telegram = $telegram->create($dbTlgParam);

        $chat = (new ChatBuilder())
            ->setId(1)
            ->setUsername('test')
            ->setFirstName('test')
            ->build();

        dd($chat->getUsername() , $telegram);
    }
}
