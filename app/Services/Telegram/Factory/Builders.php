<?php

namespace App\Services\Telegram\Factory;

use App\Builders\Telegram\Chat\Chat;
use App\Builders\Telegram\Chat\ChatBuilder;
use App\Builders\Telegram\From\From;
use App\Builders\Telegram\From\FromBuilder;
use App\Builders\Telegram\Message\Message;
use App\Builders\Telegram\Message\MessageBuilder;
use App\Builders\Telegram\Query\QueryBuilder;

abstract class Builders
{
    public function __construct(
        protected ChatBuilder    $chatBuilder,
        protected MessageBuilder $messageBuilder,
        protected QueryBuilder   $queryBuilder,
        protected FromBuilder    $fromBuilder)
    {
    }

    public function getChatDvo(mixed $chat): Chat
    {
        return $this->chatBuilder
            ->setId($chat['id'])
            ->setFirstName($chat['first_name'])
            ->setUsername($chat['username'] ?? '')
            ->setType($chat['type'])
            ->build();
    }

    public function getFromDvo(mixed $from): From
    {
        return $this->fromBuilder
            ->setId($from['id'])
            ->setFirstName($from['first_name'])
            ->setUsername($from['username'] ?? '')
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
}
