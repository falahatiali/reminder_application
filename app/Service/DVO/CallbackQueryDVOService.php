<?php

namespace App\Service\DVO;

use App\DVO\Message\CallBackQueryDVO;
use App\DVO\Message\FromDVO;
use App\DVO\Message\MessageDVO;

class CallbackQueryDVOService
{
    public function create(int        $id,
                           FromDVO    $from,
                           MessageDVO $message,
                           string     $text,
                           int        $chatInstance,
                           string     $data,
                           array      $replyMarkup): CallBackQueryDVO
    {
        return new CallBackQueryDVO($id, $from, $message, $text, $chatInstance, $data, $replyMarkup);
    }
}
