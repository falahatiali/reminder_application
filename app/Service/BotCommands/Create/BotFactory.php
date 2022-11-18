<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\ChatDVO;
use App\DVO\Message\FromDVO;
use App\DVO\Message\MessageDVO;
use App\Models\TelegramModel;
use App\Service\BotCommands\Start;
use App\Service\Contracts\CreateBotCommandsContract;
use App\Service\DVO\CallbackQueryDVOService;
use App\Service\DVO\ChatDVOService;
use App\Service\DVO\FromDVOService;
use App\Service\DVO\MessageDVOService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BotFactory
{
    public function __construct(private Request $request)
    {
    }

    /**
     * @throws Exception
     */
    public function makeObject(): CreateBotCommandsContract
    {
        try {
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

                    if ($type == 'front') {
                        return app(CreateFront::class, ['message' => $messageDvo]);
                    } elseif ($type == 'backend') {
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
                $data = $data['callback_query'];
                /** @var ChatDVO $chatDvo */
                $chatDvo = app(ChatDVOService::class)->create($data['message']['chat']);
                $fromDvo = app(FromDVOService::class)->create($data['message']['from']);
                /** @var MessageDVO $messageDvo */
                $messageDvo = app(MessageDVOService::class)->create($fromDvo, $chatDvo, $data['message']);

                $callBackQueryDVO = app(CallbackQueryDVOService::class)->create(
                    $data['id'], $fromDvo, $messageDvo, $data['message']['text'],
                    $data['chat_instance'], $data['data'],
                    $data['message']['reply_markup']);

                if (isset($data['data'])) {
                    if ($data['data'] === 'create_new_reminder') {
                        return app(CreateNewReminder::class, ['message' => $callBackQueryDVO]);
                    }
                }
            }

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        throw new Exception('exception in making object');
    }

    /**
     * @param $data
     * @return TelegramModel
     */
    private function getLastTelegramObject($data): TelegramModel
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
