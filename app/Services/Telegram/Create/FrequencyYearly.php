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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrequencyYearly implements CreateBotCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private TelegramRepositoryInterface $telegramRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Select a month";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'January', 'callback_data' => 'month_1'],
                    ['text' => 'February', 'callback_data' => 'month_2'],
                ],
                [
                    ['text' => 'March', 'callback_data' => 'month_3'],
                    ['text' => 'April', 'callback_data' => 'month_4'],
                ],
                [
                    ['text' => 'May', 'callback_data' => 'month_5'],
                    ['text' => 'June', 'callback_data' => 'month_6'],
                ],
                [
                    ['text' => 'July', 'callback_data' => 'month_7'],
                    ['text' => 'August', 'callback_data' => 'month_8'],
                ],
                [
                    ['text' => 'September', 'callback_data' => 'month_9'],
                    ['text' => 'October', 'callback_data' => 'month_10'],
                ],
                [
                    ['text' => 'November', 'callback_data' => 'month_11'],
                    ['text' => 'December', 'callback_data' => 'month_12'],
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

            $value = Date::allMappings()[$this->message->getData()];
            $expression = new MyCronExpression($value);
            if (!is_array($expression) && !is_string($expression)) {
                $expression = $expression->getParts();
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
