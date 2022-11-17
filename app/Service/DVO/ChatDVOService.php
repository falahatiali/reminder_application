<?php

namespace App\Service\DVO;

use App\DVO\Message\ChatDVO;

class ChatDVOService
{
    public function create(array $chat): ChatDVO
    {
        return new ChatDVO($chat['id'], $chat['first_name'] , $chat['username'] , $chat['type']);
    }
}
