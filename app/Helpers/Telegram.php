<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Telegram implements SocialChannelContract
{
    private string $baseUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->baseUrl .= config('services.telegram.token');
    }

    public function call($function, $parameters): object
    {
        $url = $this->baseUrl . '/' . $function;
        return Http::post($url, $parameters);
    }
}
