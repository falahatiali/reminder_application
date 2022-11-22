<?php

namespace App\Builders\Telegram\Message;

use App\Builders\Telegram\Chat\Chat;
use App\Builders\Telegram\From\From;

class MessageBuilder implements MessageBuilderInterface
{
    private Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    public function setMessageId(int $messageId): static
    {
        $this->message->setMessageId($messageId);
        return $this;
    }

    public function setFrom(From $from): static
    {
        $this->message->setFrom($from);
        return $this;
    }

    public function setChat(Chat $chat): static
    {
        $this->message->setChat($chat);
        return $this;
    }

    public function setDate(string $date): static
    {
        $this->message->setDate($date);
        return $this;
    }

    public function setText(string $text): static
    {
        $this->message->setText($text);
        return $this;
    }

    public function setUserId(int $userId): static
    {
        $this->message->setUserId($userId);
        return $this;
    }

    public function build(): Message
    {
        return $this->message;
    }
}
