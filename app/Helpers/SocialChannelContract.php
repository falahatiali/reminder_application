<?php

namespace App\Helpers;

interface SocialChannelContract
{
    public function call($function, $parameters) :object;
}
