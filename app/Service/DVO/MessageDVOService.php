<?php

namespace App\Service\DVO;

use App\DVO\Message\ChatDVO;
use App\DVO\Message\FromDVO;
use App\DVO\Message\MessageDVO;

class MessageDVOService
{
    public function create(FromDVO $from, ChatDVO $chat, array $message): MessageDVO
    {
        return new MessageDVO($message['message_id'], $from, $chat, $message['date'], $message['text']);
    }
}
