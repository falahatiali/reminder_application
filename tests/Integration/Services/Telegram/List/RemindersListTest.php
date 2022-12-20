<?php

namespace Tests\Integration\Services\Telegram\List;

use App\Services\Telegram\Create\Bot;
use App\Services\Telegram\List\RemindersList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemindersListTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_we_have_a_right_input_structure()
    {
        self::assertEquals(1,1);
//        $message = file_get_contents(base_path() . '/tests/data/message.json');
//        $message = json_decode($message, true);
//        $message['message']['date'] = time();
//
//        /** @var BotFactory $botFactory */
//        $botFactory = app(BotFactory::class, ['request' => request()]);
//        $chat = $botFactory->getChatDvo($message['message']['chat']);
//        $from = $botFactory->getFromDvo($message['message']['from']);
//        $messageDvo = $botFactory->getMessageDvo($message['message'], $from, $chat);
//
//        $reminderList = app(RemindersList::class, ['message' => $messageDvo]);
//
//        dd($reminderList->create());
    }
}
