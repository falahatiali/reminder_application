<?php

namespace App\Builders\Telegram\From;

class FromBuilder implements FromBuilderInterface
{
    private From $from;

    public function __construct()
    {
        $this->from = new From();
    }

    public function setId(string $id): static
    {
        $this->from->setId($id);

        return $this;
    }

    public function setIsBot(bool $isBot): static
    {
        $this->from->setIsBot($isBot);

        return $this;
    }


    public function setFirstName(string $firstName): static
    {
        $this->from->setFirstName($firstName);

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->from->setUsername($username);

        return $this;
    }

    public function setLanguageCode(string $languageCode): static
    {
        $this->from->setLanguageCode($languageCode);

        return $this;
    }

    public function build(): From
    {
        return $this->from;
    }
}
