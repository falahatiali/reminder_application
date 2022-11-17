<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\MessageInterface;
use App\Helpers\SocialChannelContract;

abstract class BaseCreate
{
    protected SocialChannelContract $channel;

    public function __construct(SocialChannelContract $channel)
    {
        $this->channel = $channel;
    }
}
