<?php

namespace App\Builders\Telegram\Message;

use App\Builders\Telegram\Chat\Chat;
use App\Builders\Telegram\From\From;

class Message
{
    private int $messageId;
    private From $from;
    private Chat $chat;
    private string $date;
    private string $text;
    private int $userId;

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function getFrom(): From
    {
        return $this->from;
    }

    public function setFrom(From $from): void
    {
        $this->from = $from;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }

    public function setChat(Chat $chat): void
    {
        $this->chat = $chat;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function toArray(): array
    {
        return [
            'message_id' => $this->getMessageId(),
            'from' => [
                "id" => $this->getFrom()->getId(),
                "is_bot" => $this->getFrom()->isBot(),
                "first_name" => $this->getFrom()->getFirstName(),
                "username" => $this->getFrom()->getUsername(),
                "language_code" => $this->getFrom()->getLanguageCode()
            ],
            'chat' => [
                "id" => $this->getChat()->getId(),
                "first_name" => $this->getChat()->getFirstName(),
                "username" => $this->getChat()->getUsername(),
                "type" => $this->getChat()->getType()
            ],
            'date' => $this->getDate(),
            'text' => $this->getText(),
            'user_id' => $this->getUserId()
        ];
    }
}
