<?php

namespace App\Http\Controllers;

use App\Service\BotCommands\Create\BotFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * @throws Exception
     */
    public function getWebhook(Request $request)
    {
        $botCommandFactory = app(BotFactory::class, ['request' => $request]);

        try {
            $object = $botCommandFactory->makeObject();
            return $object->create();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
