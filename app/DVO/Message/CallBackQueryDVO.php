<?php

namespace App\DVO\Message;

class CallBackQueryDVO implements MessageInterface
{
    private int $id;
    private FromDVO $from;
    private MessageDVO $message;
    private string $text;
    private int $chatInstance;
    private string $data;
    private array $replyMarkup;


    /**
     * @param int $id
     * @param FromDVO $from
     * @param MessageDVO $message
     * @param string $text
     * @param int $chatInstance
     * @param string $data
     * @param array $replyMarkup
     */
    public function __construct(int        $id,
                                FromDVO    $from,
                                MessageDVO $message,
                                string     $text,
                                int        $chatInstance,
                                string     $data,
                                array      $replyMarkup)
    {
        $this->id = $id;
        $this->from = $from;
        $this->message = $message;
        $this->text = $text;
        $this->chatInstance = $chatInstance;
        $this->data = $data;
        $this->replyMarkup = $replyMarkup;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     * @return MessageDVO
     */
    public function getMessage(): MessageDVO
    {
        return $this->message;
    }

    /**
     * @param MessageDVO $message
     */
    public function setMessage(MessageDVO $message): void
    {
        $this->message = $message;
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
     * @return int
     */
    public function getChatInstance(): int
    {
        return $this->chatInstance;
    }

    /**
     * @param int $chatInstance
     */
    public function setChatInstance(int $chatInstance): void
    {
        $this->chatInstance = $chatInstance;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
    }

    /**
     * @param array $replyMarkup
     */
    public function setReplyMarkup(array $replyMarkup): void
    {
        $this->replyMarkup = $replyMarkup;
    }

    /**
     * @return array
     */
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
