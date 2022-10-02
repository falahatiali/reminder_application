<?php

namespace App\Http\Controllers;

use App\Helpers\BotComponents\Create;
use App\Helpers\BotComponents\Start;
use App\Models\TelegramModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function getWebhook(Request $request)
    {
        Log::error(json_encode($request->all()));
        $text = $request->all();
//        $text = '{"ok":true,"result":[{"update_id":460984391,"message":{"message_id":54,"from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663226885,"text":"/start","entities":[{"offset":0,"length":6,"type":"bot_command"}]}}]}';
//        $text = '{"ok":true,"result":[{"update_id":460984392,"callback_query":{"id":"8491552156357483053","from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"message":{"message_id":55,"from":{"id":5451732155,"is_bot":true,"first_name":"Testali","username":"Aliiiiitestttbot"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663226983,"text":"Your welcome Fala!","reply_markup":{"inline_keyboard":[[{"text":"Create a new reminder","callback_data":"create_new_reminder"},{"text":"Get reminders list","callback_data":"get_reminders_list"}]]}},"chat_instance":"1694476004114037214","data":"create_new_reminder"}}]}';
//        $text = '{"ok":true,"result":[{"update_id":460984393,"message":{"message_id":57,"from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663227073,"text":"TheFirstWord"}}]}';
//        $text = '{"ok":true,"result":[{"update_id":460984394,"message":{"message_id":59,"from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663227133,"text":"لغت اول و معنی آن"}}]}';
//        $text ='{"ok":true,"result":[{"update_id":460984395,"message":{"message_id":61,"from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663227210,"text":"This is body for FirstWord"}}]}';
//        $text ='{"ok":true,"result":[{"update_id":460984396,"message":{"message_id":63,"from":{"id":1977093554,"is_bot":false,"first_name":"Fala","username":"alifala99","language_code":"en"},"chat":{"id":1977093554,"first_name":"Fala","username":"alifala99","type":"private"},"date":1663227599,"text":"This is additional text"}}]}';

        if (is_string($text)) {
            $text = json_decode($text, true);
        }

        if (Arr::has($text, 'ok') && $text['ok']) {
            foreach ($text['result'] as $index => $data) {
                if (Arr::has($data, 'message')) {
                    $chat = $data['message']['chat'];
                    switch ($data['message']['text']) {
                        case '/start':
                            $start = app(Start::class, ['data' => $data]);
                            $start->run();
                            break;
                        default:
                            $lastTelegramEntity = TelegramModel::query()->where('finish', false)
                                ->where('chat_id', $data['message']['chat']['id'])
                                ->whereDate('created_at', Carbon::today())
                                ->latest()
                                ->first();

                            $type = 'front';
                            if ($lastTelegramEntity->reminder_type == 'front') {
                                $type = 'backend';
                            } elseif ($lastTelegramEntity->reminder_type == 'backend') {
                                $type = 'body';
                            } elseif ($lastTelegramEntity->reminder_type == 'body') {
                                $type = 'additional_text';
                            }
                            return $this->createMessage($data, $type, $lastTelegramEntity);
                    }
                } elseif (Arr::has($data, 'callback_query')) {
                    $data = $data['callback_query'];
                    if (isset($data['data'])) {
                        if ($data['data'] === 'create_new_reminder') {
                            $new = app(Create::class, ['data' => $data, 'type' => 'create_new_reminder']);
                            $new->run();
                        }
                    }
                } else {
                    //create front
                    dd(11);

                }
            }
        }
    }


    /**
     * @param mixed $user
     * @return array
     */
    public function extractChatData(array $user): array
    {
        $chat['id'] = $user['id'] ?? 0;
        $chat['first_name'] = $user['first_name'] ?? '';
        $chat['username'] = $user['username'] ?? '';
        $chat['type'] = $user['type'] ?? '';

        return $chat;
    }

    /**
     * @param array $data
     * @param string $type
     * @param $lastTelegramEntity
     * @return mixed
     */
    private function createMessage(array $data, string $type, $lastTelegramEntity): mixed
    {
        $create = app(Create::class, ['data' => $data, 'type' => $type, 'telegramModel' => $lastTelegramEntity]);
        return $create->run();
    }
}
