<?php

namespace App\Services\Telegram\Factory;

use App\Models\TelegramModel;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotFinish;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Contracts\BotCommandContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessageFactory extends Builders
{
    public function create(array $data): BotCommandContract
    {
        $chat = $data['message']['chat'];
        $from = $data['message']['from'];

        $chatDvo = $this->getChatDvo($chat);
        $fromDvo = $this->getFromDvo($from);
        $messageDvo = $this->getMessageDvo($data['message'], $fromDvo, $chatDvo);

        $checkString = str_replace('/', '', $data['message']['text']);
        if (array_key_exists($checkString, config('mappings'))) {
            $className = config("mappings.{$checkString}.class");

            return app($className, ['message' => $messageDvo]);
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
