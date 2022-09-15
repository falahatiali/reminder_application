<?php

namespace App\Helpers\BotComponents;

use App\Helpers\Telegram;
use App\Models\ReminderModel;
use App\Models\TelegramModel;
use App\Models\User;
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
            'button_column' => [
                [
                    ['text' => 'Every Minute', 'callback_data' => 'everyMinute'],
                    ['text' => 'everyTwoMinutes', 'callback_data' => 'Every 2 Minutes'],
                    ['text' => 'everyThreeMinutes', 'callback_data' => 'Every 3 Minutes'],
                    ['text' => 'everyFourMinutes', 'callback_data' => 'Every 4 Minutes'],
                    ['text' => 'everyFiveMinutes', 'callback_data' => 'Every 5 Minutes'],
                    ['text' => 'everyFifteenMinutes', 'callback_data' => 'Every 15 Minutes'],
                    ['text' => 'everyThirtyMinutes', 'callback_data' => 'Every 30 Minutes'],
                    ['text' => 'everyTwoHours', 'callback_data' => 'Every 2 Hours'],
                    ['text' => 'everyThreeHours', 'callback_data' => 'Every 3 Hours'],
                    ['text' => 'everyFourHours', 'callback_data' => 'Every 4 Hours'],
                    ['text' => 'everySixHours', 'callback_data' => 'Every 6 hours'],
                    ['text' => 'hourly', 'callback_data' => 'Every hour'],
                    ['text' => 'daily', 'callback_data' => 'Every day'],
                    ['text' => 'weekly', 'callback_data' => 'Every week'],
                    ['text' => 'monthly', 'callback_data' => 'Every month'],
                    ['text' => 'yearly', 'callback_data' => 'Every year'],
                ]
            ],
            'resize_keyboard' => true
        ];

        $parameters = [
            'chat_id' => $this->data['message']['chat']['id'],
            'text' => $response,
            'reply_to_message_id' => $this->data['message']['message_id'],
            'reply_markup' => json_encode($keyboard)
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
}
