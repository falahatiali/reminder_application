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

class FrequencySelectHour implements CreateBotCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private TelegramRepositoryInterface $telegramRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Now choose minute (every 5 minutes)";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '00', 'callback_data' => 'minute_0'],
                    ['text' => '05', 'callback_data' => 'minute_5'],
                    ['text' => '10', 'callback_data' => 'minute_10'],
                    ['text' => '15', 'callback_data' => 'minute_15'],
                    ['text' => '20', 'callback_data' => 'minute_20'],
                    ['text' => '25', 'callback_data' => 'minute_25'],
                ],
                [
                    ['text' => '30', 'callback_data' => 'minute_30'],
                    ['text' => '35', 'callback_data' => 'minute_35'],
                    ['text' => '40', 'callback_data' => 'minute_40'],
                    ['text' => '45', 'callback_data' => 'minute_45'],
                    ['text' => '50', 'callback_data' => 'minute_50'],
                    ['text' => '55', 'callback_data' => 'minute_55'],
                ],
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
                'reminder_type' => 'hourly',
                'user_id' => $this->message->getMessage()->getUserId()
            ];

            $this->telegramRepository->create($dbTlgParam);

            $reminder = $this->reminderRepository->withCriteria(new IsNotComplete(), new LatestFirst())
                ->findWhere('user_id', '=', $dbTlgParam['user_id'])
                ->first();

            $expressionArray = explode(' ', $reminder->expression);
            $expressionArray[1] = explode('_', $this->message->getData())[1];

            $reminder->update([
                'expression' => implode(' ', $expressionArray)
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
