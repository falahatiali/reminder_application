<?php

namespace App\Helpers\BotComponents;

use App\Helpers\Date;
use App\Helpers\Telegram;
use App\Models\ReminderModel;
use App\Models\TelegramModel;
use App\Models\User;
use App\Scheduler\MyCronExpression;
use Exception;
use Illuminate\Support\Facades\DB;

class Create implements TelegramComponentContract
{
    private array $data;
    private string $type;
    private ?TelegramModel $telegramModel;
    private Telegram $telegram;

    public function __construct(array $data, string $type = '', TelegramModel $telegramModel = null)
    {
        $this->data = $data;
        $this->type = $type;
        $this->telegramModel = $telegramModel;

        $this->telegram = app(Telegram::class);
    }

    public function run()
    {
        if ($this->type == 'create_new_reminder') {
            return $this->sendFirstTextFrontData();
        } elseif ($this->type == 'front') {
            return $this->createFrontOfTheLeitner();
        } elseif ($this->type == 'backend') {
            return $this->createBackendOfTheCard();
        } elseif ($this->type == 'body') {
            return $this->createBodyForCard();
        } elseif ($this->type == 'additional_text') {
            return $this->createExtraText();
        } elseif ($this->type == 'frequency') {
            return $this->createFrequency();
        } elseif ($this->type == 'finish') {

        }
    }

    private function sendFirstTextFrontData()
    {
        $response = "{$this->data['message']['chat']['first_name']}, Please send your word!";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->data['message']['chat']['id'],
            'reply_to_message_id' => $this->data['message']['message_id'],
        ];

        DB::beginTransaction();
        try {
            $user = User::query()->where('telegram_id', $this->data['message']['chat']['id'])->first();

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['CALLBACK_QUERY'],
                'from_id' => $this->data['message']['from']['id'],
                'message_id' => $this->data['message']['message_id'],
                'is_bot' => $this->data['message']['from']['is_bot'],
                'first_name' => $this->data['message']['chat']['first_name'],
                'username' => $this->data['message']['chat']['username'] ?? '',
                'language_code' => $this->data['from']['language_code'] ?? '',
                'chat_id' => $this->data['message']['chat']['id'],
                'chat_type' => $this->data['message']['chat']['type'],
                'unix_timestamp' => $this->data['message']['date'],
                'chat_instance' => $this->data['chat_instance'],
                'data' => $this->data['data'],
                'text' => $this->data['message']['text'],
                'telegram' => $this->data['message'],
            ];

            $user->telegramEntity()->create($dbTlgParam);

            DB::commit();
            return $this->telegram->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            // todo - return exception
        }
    }

    private function createFrontOfTheLeitner()
    {
        $response = "{$this->data['message']['chat']['first_name']}, You successfuly set the word.🥰 Now create the back of the card. (meaning or description) 🤗";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->data['message']['chat']['id'],
            'reply_to_message_id' => $this->data['message']['message_id'],
        ];

        $dbTlgParam = [
            'type' => TelegramModel::TYPE['MESSAGE'],
            'from_id' => $this->data['message']['from']['id'],
            'message_id' => $this->data['message']['message_id'],
            'is_bot' => $this->data['message']['from']['is_bot'],
            'first_name' => $this->data['message']['chat']['first_name'],
            'username' => $this->data['message']['chat']['username'] ?? '',
            'language_code' => $this->data['from']['language_code'] ?? '',
            'chat_id' => $this->data['message']['chat']['id'],
            'chat_type' => $this->data['message']['chat']['type'],
            'unix_timestamp' => $this->data['message']['date'],
            'text' => $this->data['message']['text'],
            'telegram' => $this->data['message'],
            'reminder_type' => 'front',
            'user_id' => $userId = $this->telegramModel->user_id
        ];

        $reminderFrontParam = [
            'user_id' => $userId,
            'frontend' => $dbTlgParam['text'],
        ];

        try {
            DB::beginTransaction();
            $front = TelegramModel::query()->create($dbTlgParam);
            $reminder = ReminderModel::query()->create($reminderFrontParam);
            DB::commit();
            return $this->telegram->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            //todo throw exception
        }
    }

    private function createBackendOfTheCard()
    {
        $response = "{$this->data['message']['chat']['first_name']}, Ok. your 2 side of your card is now successfully created. please add a body 🤗";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->data['message']['chat']['id'],
            'reply_to_message_id' => $this->data['message']['message_id'],
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['MESSAGE'],
                'from_id' => $this->data['message']['from']['id'],
                'message_id' => $this->data['message']['message_id'],
                'is_bot' => $this->data['message']['from']['is_bot'],
                'first_name' => $this->data['message']['chat']['first_name'],
                'username' => $this->data['message']['chat']['username'] ?? '',
                'language_code' => $this->data['from']['language_code'] ?? '',
                'chat_id' => $this->data['message']['chat']['id'],
                'chat_type' => $this->data['message']['chat']['type'],
                'unix_timestamp' => $this->data['message']['date'],
                'text' => $this->data['message']['text'],
                'telegram' => $this->data['message'],
                'reminder_type' => 'backend',
                'user_id' => $this->telegramModel->user_id
            ];

            $backend = TelegramModel::query()->create($dbTlgParam);

            $reminder = ReminderModel::query()
                ->where('user_id', $dbTlgParam['user_id'])
                ->where('is_complete', false)
                ->update([
                    'backend' => $dbTlgParam['text']
                ]);

            DB::commit();

            return $this->telegram->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            //todo
        }
    }

    private function createBodyForCard()
    {
        $response = "{$this->data['message']['chat']['first_name']}, Ok. please add extra text if you have 🤗";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->data['message']['chat']['id'],
            'reply_to_message_id' => $this->data['message']['message_id'],
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['MESSAGE'],
                'from_id' => $this->data['message']['from']['id'],
                'message_id' => $this->data['message']['message_id'],
                'is_bot' => $this->data['message']['from']['is_bot'],
                'first_name' => $this->data['message']['chat']['first_name'],
                'username' => $this->data['message']['chat']['username'] ?? '',
                'language_code' => $this->data['from']['language_code'] ?? '',
                'chat_id' => $this->data['message']['chat']['id'],
                'chat_type' => $this->data['message']['chat']['type'],
                'unix_timestamp' => $this->data['message']['date'],
                'text' => $this->data['message']['text'],
                'telegram' => $this->data['message'],
                'reminder_type' => 'body',
                'user_id' => $this->telegramModel->user_id
            ];

            $backend = TelegramModel::query()->create($dbTlgParam);

            $reminder = ReminderModel::query()
                ->where('user_id', $dbTlgParam['user_id'])
                ->where('is_complete', false)
                ->update([
                    'body' => $dbTlgParam['text']
                ]);

            DB::commit();

            return $this->telegram->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            //todo
        }
    }

    private function createExtraText()
    {
        $response = "{$this->data['message']['chat']['first_name']}, Ok. choose the frequency 🤗";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Every Minute', 'callback_data' => 'everyMinute'],
                    ['text' => 'Every Two Minutes', 'callback_data' => 'everyTwoMinutes'],
                ],
                [
                    ['text' => 'Every Three Minutes', 'callback_data' => 'everyThreeMinutes'],
                    ['text' => 'Every Four Minutes', 'callback_data' => 'everyFourMinutes'],
                ],
                [
                    ['text' => 'Every Five Minutes', 'callback_data' => 'everyFiveMinutes'],
                    ['text' => 'Every Fifteen Minutes', 'callback_data' => 'everyFifteenMinutes'],
                ],
                [
                    ['text' => 'Every Thirty Minutes', 'callback_data' => 'everyThirtyMinutes'],
                    ['text' => 'Every Two Hours', 'callback_data' => 'everyTwoHours'],
                ],
                [
                    ['text' => 'Every Three Hours', 'callback_data' => 'everyThreeHours'],
                    ['text' => 'Every Four Hours', 'callback_data' => 'everyFourHours'],
                ],
                [
                    ['text' => 'Every Six Hours', 'callback_data' => 'everySixHours'],
                    ['text' => 'Every hour', 'callback_data' => 'hourly'],
                ],
                [
                    ['text' => 'Every day', 'callback_data' => 'daily'],
                    ['text' => 'Every week', 'callback_data' => 'weekly'],
                ],
                [
                    ['text' => 'Every month', 'callback_data' => 'monthly'],
                    ['text' => 'Every year', 'callback_data' => 'yearly'],
                ]
            ],
            'resize_keyboard' => true
        ];

        $parameters = [
            'chat_id' => $this->data['message']['chat']['id'],
            'text' => $response,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $this->data['message']['message_id'],
            'reply_markup' => json_encode($keyboard)
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['CALLBACK_QUERY'],
                'from_id' => $this->data['message']['from']['id'],
                'message_id' => $this->data['message']['message_id'],
                'is_bot' => $this->data['message']['from']['is_bot'],
                'first_name' => $this->data['message']['chat']['first_name'],
                'username' => $this->data['message']['chat']['username'] ?? '',
                'language_code' => $this->data['from']['language_code'] ?? '',
                'chat_id' => $this->data['message']['chat']['id'],
                'chat_type' => $this->data['message']['chat']['type'],
                'unix_timestamp' => $this->data['message']['date'],
                'text' => $this->data['message']['text'],
                'telegram' => $this->data['message'],
                'reminder_type' => 'additional_text',
                'user_id' => $this->telegramModel->user_id
            ];

            $backend = TelegramModel::query()->create($dbTlgParam);

            $reminder = ReminderModel::query()
                ->where('user_id', $dbTlgParam['user_id'])
                ->where('is_complete', false)
                ->update([
                    'additional_text' => $dbTlgParam['text']
                ]);

            DB::commit();

            return $this->telegram->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            //todo
        }
    }

    private function createFrequency()
    {
        $response = "{$this->data['message']['chat']['first_name']}, Almost done 🤗";

        $parameters = [
            'chat_id' => $this->data['message']['chat']['id'],
            'text' => $response,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $this->data['message']['message_id'],
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['MESSAGE'],
                'from_id' => $this->data['message']['from']['id'],
                'message_id' => $this->data['message']['message_id'],
                'is_bot' => $this->data['message']['from']['is_bot'],
                'first_name' => $this->data['message']['chat']['first_name'],
                'username' => $this->data['message']['chat']['username'] ?? '',
                'language_code' => $this->data['from']['language_code'] ?? '',
                'chat_id' => $this->data['message']['chat']['id'],
                'chat_type' => $this->data['message']['chat']['type'],
                'unix_timestamp' => $this->data['message']['date'],
                'text' => $this->data['message']['text'],
                'telegram' => $this->data['message'],
                'reminder_type' => 'frequency',
                'finish' => true,
                'user_id' => $this->telegramModel->user_id
            ];

            $backend = TelegramModel::query()->create($dbTlgParam);

            $value = Date::allMappings()[$this->data['data']];
            $expression = new MyCronExpression($value);
            if (!is_array($expression) && !is_string($expression)) {
                $expression = $expression->getParts();
                $expression = implode(' ', $expression);
            }

            $reminder = ReminderModel::query()
                ->where('user_id', $dbTlgParam['user_id'])
                ->where('is_complete', false)
                ->update([
                    'expression' => $expression,
                    'frequency'  => $this->data['data'],
                    'is_complete' => true
                ]);

            DB::commit();

            return $this->telegram->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            //todo
        }
    }
}
