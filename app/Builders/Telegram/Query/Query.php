<?php

namespace App\Builders\Telegram\Query;

use App\Builders\Telegram\From\From;
use App\Builders\Telegram\Message\Message;

class Query
{
    private int $id;
    private From $from;
    private Message $message;
    private string $text;
    private int $chatInstance;
    private string $data;
    private array $replyMarkup;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFrom(): From
    {
        return $this->from;
    }

    public function setFrom(From $from): void
    {
        $this->from = $from;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getChatInstance(): int
    {
        return $this->chatInstance;
    }

    public function setChatInstance(int $chatInstance): void
    {
        $this->chatInstance = $chatInstance;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
    }

    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'from' => [
                "id" => $this->getFrom()->getId(),
                "is_bot" => $this->getFrom()->isBot(),
                "first_name" => $this->getFrom()->getFirstName(),
                "username" => $this->getFrom()->getUsername(),
                "language_code" => $this->getFrom()->getLanguageCode()
            ],
            'message' => $this->getMessage()->toArray(),
            'text' => $this->getText(),
            'chatInstance' => $this->getChatInstance(),
            'data' => $this->getData(),
            'replyMarkup' => $this->getReplyMarkup(),
        ];
    }
}
