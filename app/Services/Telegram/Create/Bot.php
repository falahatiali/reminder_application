<?php

namespace App\Services\Telegram\Create;

use App\Services\Telegram\Factory\Factory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Bot
{
    public function __construct(private Request $request)
    {
    }

    /**
     * @throws Exception
     */
    public function makeObject()
    {
        try {
            $text = $this->request->all();

            // For Log input data
            Log::error(is_string($text) ? $text : json_encode($text));

            $data = is_string($text) ? json_decode($text, true) : $text;
            /** @var Factory $factory */
            $factory = app(Factory::class, ['data' => $data]);
            return $factory->createObject();

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        /** TODO */
        throw new Exception('exception in making object');
    }
}
