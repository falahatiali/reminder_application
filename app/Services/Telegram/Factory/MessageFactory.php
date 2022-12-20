<?php

namespace App\Services\Telegram\Factory;

use App\Models\TelegramModel;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotFinish;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Contracts\BotCommandContract;
use App\Services\Telegram\Delete\DeleteAll;
use App\Services\Telegram\List\RemindersList;
use App\Services\Telegram\Start;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessageFactory extends Builders
{
    private const TELEGRAM_COMMANDS = [
        'start' => '/start',
        'list' => '/list',
        'deleteAll' => '/deleteAll'
    ];

    public function create(array $data): BotCommandContract
    {
        $chat = $data['message']['chat'];
        $from = $data['message']['from'];

        $chatDvo = $this->getChatDvo($chat);
        $fromDvo = $this->getFromDvo($from);
        $messageDvo = $this->getMessageDvo($data['message'], $fromDvo, $chatDvo);
        
        if ($data['message']['text'] == self::TELEGRAM_COMMANDS['start']) {
            return app(Start::class, ['message' => $messageDvo]);
        } elseif ($data['message']['text'] == self::TELEGRAM_COMMANDS['list']) {
            return app(RemindersList::class, ['message' => $messageDvo]);
        } elseif ($data['message']['text'] == Str::lower(self::TELEGRAM_COMMANDS['deleteAll'])) {
            return app(DeleteAll::class, ['message' => $messageDvo]);
        } else {
            $last = $this->getLastTelegramObject($data);
            if ($last) {
                $type = TelegramModel::REMINDER_TYPE[$last->reminder_type];
                $messageDvo->setUserId($last->user_id);
                $className = 'App\\Services\\Telegram\\Create\\' . ucfirst(Str::camel($type));
                return app($className, ['message' => $messageDvo]);
            }
        }
    }

    private function getLastTelegramObject($data): TelegramModel|null
    {
        /** @var TelegramRepositoryInterface $telegramRepository */
        $telegramRepository = app(TelegramRepositoryInterface::class);

        return $telegramRepository
            ->withCriteria(new LatestFirst(), new IsNotFinish())
            ->where('chat_id', '=', $data['message']['chat']['id'])
            ->findWhere('created_at', '>=', Carbon::today())
            ->first();
    }
}
