<?php

namespace App\Http\Controllers;

use App\Service\BotCommands\Create\BotFactory;

class TelegramWebhookController extends Controller
{
    public function getWebhook()
    {
        /** @var BotFactory $botCommandFactory */
        $botCommandFactory = app(BotFactory::class);

        $object = $botCommandFactory->makeObject();
        return $object->create();
    }
}
