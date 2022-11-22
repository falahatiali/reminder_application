<?php

namespace App\Services\Telegram\Create;

use App\Builders\Telegram\Chat\Chat;
use App\Builders\Telegram\Chat\ChatBuilder;
use App\Builders\Telegram\From\From;
use App\Builders\Telegram\From\FromBuilder;
use App\Builders\Telegram\Message\Message;
use App\Builders\Telegram\Message\MessageBuilder;
use App\Builders\Telegram\Query\Query;
use App\Builders\Telegram\Query\QueryBuilder;
use App\Helpers\Date;
use App\Models\TelegramModel;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotFinish;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Contracts\CreateBotCommandsContract;
use App\Services\Telegram\Start;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class BotFactory
{
    public function __construct(
        private Request                     $request,
        private TelegramRepositoryInterface $telegramRepository,
        private UserRepositoryInterface     $userRepository,
        private ChatBuilder                 $chatBuilder,
        private MessageBuilder              $messageBuilder,
        private QueryBuilder                $queryBuilder,
        private FromBuilder                 $fromBuilder)
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
                return $this->getMessageTypeObject($data);
            } elseif (Arr::has($data, 'callback_query')) {
                return $this->getCallbackQueryObject($data);
            }

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        /** TODO */
        throw new Exception('exception in making object');
    }

    /**
     * @param mixed $data
     * @return mixed
     * @throws Exception
     */
    public function getMessageTypeObject(array $data): mixed
    {
        $chat = $data['message']['chat'];
        $from = $data['message']['from'];

        $chatDvo = $this->getChatDvo($chat);

        $fromDvo = $this->getFromDvo($from);

        $messageDvo = $this->getMessageDvo($data['message'], $fromDvo, $chatDvo);

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
            }
        }

        /** TODO */
        throw new Exception();
    }

    private function getCallbackQueryObject(array $data)
    {
        $data = $data['callback_query'];

        $chatDvo = $this->getChatDvo($data['message']['chat']);
        $fromDvo = $this->getFromDvo($data['message']['from']);
        $messageDvo = $this->getMessageDvo($data['message'], $fromDvo, $chatDvo);
        $messageDvo->setUserId($this->getUserDataId($chatDvo->getId()));

        $callBackQueryDVO = $this->getQueryDvo($data, $fromDvo, $messageDvo);

        if (isset($data['data'])) {
            if ($data['data'] === 'create_new_reminder') {
                return app(CreateNewReminder::class, ['message' => $callBackQueryDVO]);
            } elseif (Arr::exists(Date::frequencies(), $data['data'])) {
                if (1 == 2) {
                    dd(1);
                } else {
                    return app(CreateFrequency::class, ['data' => $callBackQueryDVO]);
                }

            }
        }

        /** TODO */
        throw new Exception();
    }

    private function getLastTelegramObject($data): TelegramModel
    {
        return $this->telegramRepository
            ->withCriteria(new LatestFirst(), new IsNotFinish())
            ->where('chat_id', '=', $data['message']['chat']['id'])
            ->findWhere('created_at', '>=', Carbon::today())
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

    public function getUserDataId(int $chatId)
    {
        return $this->userRepository
            ->findWhere('telegram_id', '=', $chatId)
            ->first()
            ->id;
    }

    public function getChatDvo(mixed $chat): Chat
    {
        return $this->chatBuilder
            ->setId($chat['id'])
            ->setFirstName($chat['first_name'])
            ->setUsername('username')
            ->setType($chat['type'])
            ->build();
    }

    public function getFromDvo(mixed $from): From
    {
        return $this->fromBuilder
            ->setId($from['id'])
            ->setFirstName($from['first_name'])
            ->setUsername($from['username'])
            ->setLanguageCode($from['language_code'] ?? 'en')
            ->setIsBot($from['is_bot'] ?? false)
            ->build();
    }

    public function getMessageDvo($message, From $fromDvo, Chat $chatDvo): Message
    {
        return $this->messageBuilder
            ->setMessageId($message['message_id'])
            ->setFrom($fromDvo)
            ->setChat($chatDvo)
            ->setText($message['text'])
            ->setDate($message['date'])
            ->build();
    }

    public function getQueryDvo(mixed $data, From $fromDvo, Message $messageDvo): Query
    {
        return $this->queryBuilder
            ->setId($data['id'])
            ->setFrom($fromDvo)
            ->setMessage($messageDvo)
            ->setText($data['message']['text'])
            ->setData($data['data'])
            ->setChatInstance($data['chat_instance'])
            ->setReplyMarkup($data['message']['reply_markup'])
            ->build();
    }
}
