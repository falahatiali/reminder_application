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

class FrequencyMonthly implements CreateBotCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private TelegramRepositoryInterface $telegramRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Select a day in a month (1 until 30)";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '1', 'callback_data' => 'day_in_month_1'],
                    ['text' => '2', 'callback_data' => 'day_in_month_2'],
                    ['text' => '3', 'callback_data' => 'day_in_month_3'],
                    ['text' => '4', 'callback_data' => 'day_in_month_4'],
                    ['text' => '5', 'callback_data' => 'day_in_month_5'],
                ],
                [
                    ['text' => '6', 'callback_data' => 'day_in_month_6'],
                    ['text' => '7', 'callback_data' => 'day_in_month_7'],
                    ['text' => '8', 'callback_data' => 'day_in_month_8'],
                    ['text' => '9', 'callback_data' => 'day_in_month_9'],
                    ['text' => '10', 'callback_data' => 'day_in_month_10'],
                ],
                [
                    ['text' => '11', 'callback_data' => 'day_in_month_11'],
                    ['text' => '12', 'callback_data' => 'day_in_month_12'],
                    ['text' => '13', 'callback_data' => 'day_in_month_13'],
                    ['text' => '14', 'callback_data' => 'day_in_month_14'],
                    ['text' => '15', 'callback_data' => 'day_in_month_15'],
                ],
                [
                    ['text' => '16', 'callback_data' => 'day_in_month_16'],
                    ['text' => '17', 'callback_data' => 'day_in_month_17'],
                    ['text' => '18', 'callback_data' => 'day_in_month_18'],
                    ['text' => '19', 'callback_data' => 'day_in_month_19'],
                    ['text' => '20', 'callback_data' => 'day_in_month_20'],
                ],
                [
                    ['text' => '21', 'callback_data' => 'day_in_month_21'],
                    ['text' => '22', 'callback_data' => 'day_in_month_22'],
                    ['text' => '23', 'callback_data' => 'day_in_month_23'],
                    ['text' => '24', 'callback_data' => 'day_in_month_24'],
                    ['text' => '25', 'callback_data' => 'day_in_month_25'],
                ],
                [
                    ['text' => '26', 'callback_data' => 'day_in_month_26'],
                    ['text' => '27', 'callback_data' => 'day_in_month_27'],
                    ['text' => '28', 'callback_data' => 'day_in_month_28'],
                    ['text' => '29', 'callback_data' => 'day_in_month_29'],
                    ['text' => '30', 'callback_data' => 'day_in_month_30'],
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
                'reminder_type' => 'monthly',
                'user_id' => $this->message->getMessage()->getUserId()
            ];

            $this->telegramRepository->create($dbTlgParam);

            if (preg_match("/^month_[0-9]*[0-9]$/", $this->message->getData())) {
                $month = Str::replace('month_', '', $this->message->getData());
                $this->message->setData('monthly');
            }

            $value = Date::allMappings()[$this->message->getData()];
            $expression = new MyCronExpression($value);
            if (!is_array($expression) && !is_string($expression)) {
                $expression = $expression->getParts();
                if (isset($month)) {
                    $expression[3] = $month;
                }
                $expression = implode(' ', $expression);
            }

            $this->reminderRepository->withCriteria(new IsNotComplete(), new LatestFirst())
                ->updateWhere('user_id', '=', $dbTlgParam['user_id'], [
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
