<?php

namespace App\Http\Controllers;

use App\Builders\Telegram\Chat\ChatBuilder;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Support\Facades\Http;

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

        $client = Http::withToken(env('OPENAI_SECRET'))
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/completions', [
                "model" => "text-davinci-003",
                "prompt" => "The following is a conversation with an AI assistant.
                The assistant is helpful, creative, clever, and very friendly.\n\nHuman:
                Hello, who are you?\nAI: I am an AI created by OpenAI. How can I help you today
                ?\nHuman: I'd like to cancel my subscription.\nAI:",
                "temperature" => 0.9,
                "max_tokens" => 150,
                "top_p" => 1,
                "frequency_penalty" => 0.0,
                "presence_penalty" => 0.6,
                "stop" => [" Human:", " AI:"]
            ]);

        dd($client->body());
    }
}
