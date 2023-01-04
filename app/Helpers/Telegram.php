<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $response = Http::post($url, $parameters);

        /** TODO for debugging */
//        Log::error('*******************************************');
//        Log::error($response->body());
//        Log::error('********************************************');

        return $response;
    }
}
