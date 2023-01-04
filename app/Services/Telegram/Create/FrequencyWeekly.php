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

class FrequencyWeekly implements CreateBotCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private TelegramRepositoryInterface $telegramRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Now choose a day: ";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Saturday', 'callback_data' => 'day_6'],
                    ['text' => 'Sunday', 'callback_data' => 'day_0'],
                    ['text' => 'Monday', 'callback_data' => 'day_1'],
                ],
                [
                    ['text' => 'Tuesday', 'callback_data' => 'day_2'],
                    ['text' => 'Wednesday', 'callback_data' => 'day_3'],
                ],
                [
                    ['text' => 'Thursday', 'callback_data' => 'day_4'],
                    ['text' => 'Friday', 'callback_data' => 'day_5'],
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
                'reminder_type' => 'weekly',
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
