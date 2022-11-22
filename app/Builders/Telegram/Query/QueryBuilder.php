<?php

namespace App\Builders\Telegram\Query;

use App\Builders\Telegram\From\From;
use App\Builders\Telegram\Message\Message;

class QueryBuilder implements QueryBuilderInterface
{
    private Query $query;

    public function __construct()
    {
        $this->query = new Query();
    }

    public function setId(int $id): static
    {
        $this->query->setId($id);
        return $this;
    }

    public function setFrom(From $from): static
    {
        $this->query->setFrom($from);
        return $this;
    }

    public function setMessage(Message $message): static
    {
        $this->query->setMessage($message);
        return $this;
    }

    public function setText(string $text): static
    {
        $this->query->setText($text);
        return $this;
    }

    public function setChatInstance(int $chatInstance): static
    {
        $this->query->setChatInstance($chatInstance);
        return $this;
    }

    public function setData(string $data): static
    {
        $this->query->setData($data);
        return $this;
    }

    public function setReplyMarkup(array $replyMarkup): static
    {
        $this->query->setReplyMarkup($replyMarkup);
        return $this;
    }

    public function build(): Query
    {
        return $this->query;
    }
}
