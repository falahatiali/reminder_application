<?php

namespace App\Builders\Telegram\Chat;

class ChatBuilder
{
    private Chat $chat;

    public function __construct()
    {
        $this->chat = new Chat();
    }

    public function setId($id): static
    {
        $this->chat->setId($id);

        return $this;
    }

    public function setFirstName(string $firstName): static
    {
        $this->chat->setFirstName($firstName);

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->chat->setUsername($username);

        return $this;
    }

    public function setType(string $type): static
    {
        $this->chat->setType($type);

        return $this;
    }

    public function build(): Chat
    {
        return $this->chat;
    }
}
