<?php

namespace App\DVO\Message;

class FromDVO implements MessageInterface
{
    private int $id;
    private bool $isBot;
    private string $firstName;
    private string $username;
    private string $languageCode;

    public function __construct(int    $id,
                                string $firstName,
                                string $username,
                                string $languageCode = 'en',
                                bool   $isBot = false)
    {
        $this->id = $id;
        $this->isBot = $isBot;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->languageCode = $languageCode;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isBot(): bool
    {
        return $this->isBot;
    }

    /**
     * @param bool $isBot
     */
    public function setIsBot(bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }
}
