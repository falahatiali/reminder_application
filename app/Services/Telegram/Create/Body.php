<?php

namespace App\Services\Telegram\Create;

use App\Builders\Telegram\Message\Message;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Contracts\CreateBotCommandContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Body implements CreateBotCommandContract
{
    public function __construct(
        private Message                     $message,
        private SocialChannelContract       $channel,
        private TelegramRepositoryInterface $telegramRepository,
        private ReminderRepositoryInterface $reminderRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getChat()->getFirstName()}, Ok. please add extra text if you have 🤗";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->message->getChat()->getId(),
            'reply_to_message_id' => $this->message->getMessageId(),
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['MESSAGE'],
                'from_id' => $this->message->getFrom()->getId(),
                'message_id' => $this->message->getMessageId(),
                'is_bot' => $this->message->getFrom()->isBot(),
                'first_name' => $this->message->getChat()->getFirstName(),
                'username' => $this->message->getChat()->getUsername() ?? '',
                'language_code' => $this->message->getFrom()->getLanguageCode() ?? '',
                'chat_id' => $this->message->getChat()->getId(),
                'chat_type' => $this->message->getChat()->getType(),
                'unix_timestamp' => $this->message->getDate(),
                'text' => $this->message->getText(),
                'telegram' => $this->message->toArray(),
                'reminder_type' => 'body',
                'user_id' => $this->message->getUserId()
            ];

            $this->telegramRepository->create($dbTlgParam);

            $this->reminderRepository->withCriteria(new IsNotComplete(), new LatestFirst())
                ->updateWhere('user_id', '=', $dbTlgParam['user_id'], [
                    'body' => $dbTlgParam['text']
                ]);

            DB::commit();

            return $this->channel->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' - '. $exception->getFile() . ' - '. $exception->getLine());
            //todo
        }
    }
}
