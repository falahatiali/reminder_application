<?php

namespace App\Services\Telegram\Factory;

use App\Builders\Telegram\From\From;
use App\Builders\Telegram\Message\Message;
use App\Builders\Telegram\Query\Query;
use App\Helpers\Date;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\BotCommandContract;
use App\Services\Telegram\Create\Frequency;
use App\Services\Telegram\Create\NewReminder;
use App\Services\Telegram\Delete\DeleteReminder;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class QueryFactory extends Builders
{
    public function create(array $query): BotCommandContract
    {
        $chatDvo = $this->getChatDvo($query['message']['chat']);
        $fromDvo = $this->getFromDvo($query['message']['from']);
        $messageDvo = $this->getMessageDvo($query['message'], $fromDvo, $chatDvo);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = app(UserRepositoryInterface::class);
        $userId = $userRepository
            ->findWhere('telegram_id', '=', $chatDvo->getId())
            ->first()
            ->id;

        $messageDvo->setUserId($userId);

        $callBackQueryDVO = $this->getQueryDvo($query, $fromDvo, $messageDvo);

        if (isset($query['data'])) {
            if (array_key_exists($query['data'], config('mappings'))) {

                $className = config("mappings.{$query['data']}.class");
                return app($className, ['message' => $callBackQueryDVO]);

            } elseif (Arr::exists(Date::frequencies(), $query['data'])) {
                if (1 == 2) {
                    dd(1);
                } else {
                    return app(Frequency::class, ['data' => $callBackQueryDVO]);
                }

            }
        }

        /** TODO */
        throw new Exception();
    }

    private function getQueryDvo(mixed $data, From $fromDvo, Message $messageDvo): Query
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
