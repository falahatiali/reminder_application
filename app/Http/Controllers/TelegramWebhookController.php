<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Create\Bot;
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
        /** @var Bot $botCommandFactory */
        $botCommandFactory = app(Bot::class, ['request' => $request]);

        try {
            $object = $botCommandFactory->makeObject();
            return $object->action();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
