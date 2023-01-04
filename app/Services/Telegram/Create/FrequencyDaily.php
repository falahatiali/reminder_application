<?php

namespace App\Services\Telegram\Create;

use App\Builders\Telegram\Query\Query;
use App\Helpers\Date;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Scheduler\MyCronExpression;
use App\Services\Contracts\CreateBotCommandContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FrequencyDaily implements CreateBotCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private TelegramRepositoryInterface $telegramRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Now choose hour (Hour is between 00 until 23)";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '00', 'callback_data' => 'hour_0'],
                    ['text' => '01', 'callback_data' => 'hour_1'],
                    ['text' => '02', 'callback_data' => 'hour_2'],
                    ['text' => '03', 'callback_data' => 'hour_3'],
                    ['text' => '04', 'callback_data' => 'hour_4'],
                ],
                [
                    ['text' => '05', 'callback_data' => 'hour_5'],
                    ['text' => '06', 'callback_data' => 'hour_6'],
                    ['text' => '07', 'callback_data' => 'hour_7'],
                    ['text' => '08', 'callback_data' => 'hour_8'],
                    ['text' => '09', 'callback_data' => 'hour_9'],
                ],
                [
                    ['text' => '10', 'callback_data' => 'hour_10'],
                    ['text' => '11', 'callback_data' => 'hour_11'],
                    ['text' => '12', 'callback_data' => 'hour_12'],
                    ['text' => '13', 'callback_data' => 'hour_13'],
                    ['text' => '14', 'callback_data' => 'hour_14'],
                ],
                [
                    ['text' => '15', 'callback_data' => 'hour_15'],
                    ['text' => '16', 'callback_data' => 'hour_16'],
                    ['text' => '17', 'callback_data' => 'hour_17'],
                    ['text' => '18', 'callback_data' => 'hour_18'],
                ],
                [
                    ['text' => '19', 'callback_data' => 'hour_19'],
                    ['text' => '20', 'callback_data' => 'hour_20'],
                    ['text' => '21', 'callback_data' => 'hour_21'],
                    ['text' => '22', 'callback_data' => 'hour_22'],
                    ['text' => '23', 'callback_data' => 'hour_23'],
                ]
            ],
            'resize_keyboard' => true
        ];

        DB::beginTransaction();
        try {
            $dbTlgParam = [
                'type' => TelegramModel::TYPE['CALLBACK_QUERY'],
                'from_id' => $this->message->getFrom()->getId(),
                'message_id' => $this->message->getMessage()->getMessageId(),
                'is_bot' => $this->message->getFrom()->isBot(),
                'first_name' => $this->message->getMessage()->getChat()->getFirstName(),
                'username' => $this->message->getMessage()->getChat()->getUsername() ?? '',
                'language_code' => $this->message->getFrom()->getLanguageCode() ?? '',
                'chat_id' => $this->message->getMessage()->getChat()->getId(),
                'chat_type' => $this->message->getMessage()->getChat()->getType(),
                'unix_timestamp' => $this->message->getMessage()->getDate(),
                'text' => $this->message->getText(),
                'telegram' => $this->message->toArray(),
                'reminder_type' => 'daily',
                'user_id' => $this->message->getMessage()->getUserId()
            ];

            $reminder = $this->reminderRepository
                ->withCriteria(new IsNotComplete(), new LatestFirst())
                ->findWhere('user_id', '=', $dbTlgParam['user_id']);

            if (preg_match("/^day_[0-9]$/", $this->message->getData())) {
                $day = explode('_', $this->message->getData())[1];
                $this->message->setData('daily');
            }

            if (preg_match("/^day_in_month_[0-9]*[0-9]$/", $this->message->getData())) {
                $dayInMonth = Str::replace('day_in_month_', '', $this->message->getData());
                $this->message->setData('daily');
            }

            $this->telegramRepository->create($dbTlgParam);

            $value = Date::allMappings()[$this->message->getData()];
            $expression = new MyCronExpression($value);

            if (!is_array($expression) && !is_string($expression)) {
                $expression = $expression->getParts();

                if (isset($day)) {
                    $expression[4] = $day;
                }

                $existingExpression = $reminder->first()->expression;

                if (isset($dayInMonth)) {
                    $expression[2] = $dayInMonth;
                }

                $mon = explode(' ', $existingExpression)[3];
                if (is_numeric($mon)) {
                    $expression[3] = $mon;
                }

                $expression = implode(' ', $expression);
            }

            $reminder->update([
                'expression' => $expression
            ]);

            DB::commit();
            $parameters = [
                'chat_id' => $this->message->getMessage()->getChat()->getId(),
                'text' => $response,
                'parse_mode' => 'HTML',
                'reply_to_message_id' => $this->message->getMessage()->getMessageId(),
                'reply_markup' => json_encode($keyboard)
            ];

            return $this->channel->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' - ' . $exception->getFile() . ' - ' . $exception->getLine());
            // TODO
            return false;
        }
    }
}
