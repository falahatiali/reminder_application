<?php

namespace App\Http\Controllers;

use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsComplete;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\IsNotFinish;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\Today;
use Carbon\Carbon;

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
            'telegram' => [],
            'reminder_type' => 'backend',
            'user_id' => 1
        ];

        $telegramRepository = app(TelegramRepositoryInterface::class);

//        TelegramModel::query()->dd();
        $res = $telegramRepository
            ->withCriteria(new LatestFirst(), new IsNotFinish())
            ->where('chat_id', 1668964419)
            ->findWhere('created_at', '>=', Carbon::today())
            ->get()
//            ->get()
        ;

        dd($res);
    }
}
