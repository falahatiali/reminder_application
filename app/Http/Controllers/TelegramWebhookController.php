<?php

namespace App\Http\Controllers;

use App\Service\BotCommands\Create\BotFactory;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function getWebhook(Request $request)
    {
        $botCommandFactory = new BotFactory($request);

        $object = $botCommandFactory->makeObject();
        return $object->create();
    }
}
