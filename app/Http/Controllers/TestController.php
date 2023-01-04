<?php

namespace App\Http\Controllers;

use App\Builders\Telegram\Chat\ChatBuilder;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;

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

        /** @var ReminderRepositoryInterface $reminderRepo */
        $reminderRepo = app(ReminderRepositoryInterface::class);

        $re = $reminderRepo->withCriteria(new IsNotComplete(), new LatestFirst())
        ->findWhere('user_id' , '=' , 1)
        ->update([
            'day' => 13
        ]);


        dd($re);
//
//        $social = app(SocialChannelContract::class);
//        $parameters = [
//            'chat_id' => 1977093554,
//            'text' => time(),
//            'parse_mode' => 'HTML',
//        ];
//
//        return $social->call('sendMessage', $parameters)->body();
    }
}
