<?php

namespace App\Services\Telegram\Factory;

use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\BotCommandContract;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Factory
{
    public function __construct(protected array                       $data,
                                protected TelegramRepositoryInterface $telegramRepository,
                                protected UserRepositoryInterface     $userRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function createObject(): BotCommandContract
    {
        if (Arr::has($this->data, 'message')) {
            $data = $this->data;

            /** @var MessageFactory $object */
            $object = app(MessageFactory::class);

        } elseif (Arr::has($this->data, 'callback_query')) {
            $data = $this->data['callback_query'];

            /** @var QueryFactory $object */
            $object = app(QueryFactory::class);

        } else {
            throw new Exception();
        }

        return $object->create($data);
    }
}
