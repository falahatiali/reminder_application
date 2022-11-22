<?php

namespace App\Builders\Telegram\Message;

interface MessageBuilderInterface
{
    public function build(): Message;
}
