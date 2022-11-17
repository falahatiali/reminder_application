<?php

namespace App\DVO\Message;

class MessageDVO implements MessageInterface
{
    private int $messageId;
    private FromDVO $from;
    private ChatDVO $chat;
    private string $date;
    private string $text;
    private int $userId;

    public function __construct(int     $messageId,
                                FromDVO $from,
                                ChatDVO $chat,
                                string  $date,
                                string  $text,
                                int     $userId = null)
    {
        $this->messageId = $messageId;
        $this->from = $from;
        $this->chat = $chat;
        $this->date = $date;
        $this->text = $text;
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @param int $messageId
     */
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return FromDVO
     */
    public function getFrom(): FromDVO
    {
        return $this->from;
    }

    /**
     * @param FromDVO $from
     */
    public function setFrom(FromDVO $from): void
    {
        $this->from = $from;
    }

    /**
     * @return ChatDVO
     */
    public function getChat(): ChatDVO
    {
        return $this->chat;
    }

    /**
     * @param ChatDVO $chat
     */
    public function setChat(ChatDVO $chat): void
    {
        $this->chat = $chat;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return array
     */
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

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

}
