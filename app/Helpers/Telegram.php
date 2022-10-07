<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Telegram
{
    private string $baseUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->baseUrl .= config('services.telegram.token');
    }

    public function call($function, $parameters)
    {
        $url = $this->baseUrl . '/' . $function;
        return Http::post($url, $parameters);
    }
}
