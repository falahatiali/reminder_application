<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\ChatDVO;
use App\DVO\Message\FromDVO;
use App\DVO\Message\MessageDVO;
use App\Models\TelegramModel;
use App\Service\BotCommands\Start;
use App\Service\DVO\CallbackQueryDVOService;
use App\Service\DVO\ChatDVOService;
use App\Service\DVO\FromDVOService;
use App\Service\DVO\MessageDVOService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

abstract class BotFactory
{
    public abstract function create();

    public function __construct(private Request $request)
    {
    }

    public function makeObject()
    {
        $text = $this->request->all();
        // For Log input data
        Log::error(is_string($text) ? $text : json_encode($text));

        $data = is_string($text) ? json_decode($text, true) : $text;

        if (Arr::has($data, 'message')) {
            $chat = $data['message']['chat'];
            $from = $data['message']['from'];

            /** @var ChatDVO $chatDvo */
            $chatDvo = app(ChatDVOService::class)->create($chat);
            /** @var FromDVO $fromDvo */
            $fromDvo = app(FromDVOService::class)->create($from);
            /** @var MessageDVO $messageDvo */
            $messageDvo = app(MessageDVOService::class)->create($fromDvo, $chatDvo, $data['message']);

            if ($data['message']['text'] == '/start') {
                return app(Start::class, ['message' => $messageDvo]);
            } else {
                $last = $this->getLastTelegramObject($data);
                $type = $this->getType($last);
                $messageDvo->setUserId($last->user_id);

                if ($type == 'backend') {
                    return app(CreateBackend::class, ['message' => $messageDvo]);
                } elseif ($type == 'body') {
                    return app(CreateBody::class, ['message' => $messageDvo]);
                } elseif ($type == 'additional_text') {
                    return app(CreateAdditionalText::class, ['message' => $messageDvo]);
                } elseif ($type == 'frequency') {
                    return app(CreateFrequency::class, ['data' => $messageDvo]);
                }
            }
        } elseif (Arr::has($data, 'callback_query')) {
            /** @var ChatDVO $chatDvo */
            $chatDvo = app(ChatDVOService::class)->create($data['callback_query']['message']['chat']);
            $fromDvo = app(FromDVOService::class)->create($data['callback_query']['message']['from']);
            /** @var MessageDVO $messageDvo */
            $messageDvo = app(MessageDVOService::class)->create($fromDvo, $chatDvo, $data['callback_query']['message']);

            $callBackQueryDVO = app(CallbackQueryDVOService::class)->create(
                $data['callback_query']['id'], $fromDvo, $messageDvo, $data['callback_query']['message']['text'],
                $data['callback_query']['chat_instance'], $data['callback_query']['data'],
                $data['callback_query']['message']['reply_markup']);

            if (isset($data['data'])) {
                if ($data['data'] === 'create_new_reminder') {
                    return app(CreateFront::class, ['message' => $callBackQueryDVO]);
                }
            }
        }

        return '';
    }

    /**
     * @param $data
     * @return Collection
     */
    private function getLastTelegramObject($data): Collection
    {
        return TelegramModel::where('finish', false)
            ->where('chat_id', $data['message']['chat']['id'])
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->first();
    }

    public function getType($lastTelegramEntity): string
    {
        $type = 'front';
        if ($lastTelegramEntity->reminder_type == 'front') {
            $type = 'backend';
        } elseif ($lastTelegramEntity->reminder_type == 'backend') {
            $type = 'body';
        } elseif ($lastTelegramEntity->reminder_type == 'body') {
            $type = 'additional_text';
        } elseif ($lastTelegramEntity->reminder_type == 'additional_text') {
            $type = 'frequency';
        }
        return $type;
    }
}
