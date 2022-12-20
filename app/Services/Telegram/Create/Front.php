<?php

namespace App\Services\Telegram\Create;

use App\Builders\Telegram\Message\Message;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Services\Contracts\CreateBotCommandContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Front implements CreateBotCommandContract
{
    public function __construct(
        private SocialChannelContract       $channel,
        private Message                     $message,
        private TelegramRepositoryInterface $telegramRepository,
        private ReminderRepositoryInterface $reminderRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getChat()->getFirstName()}, You successfully set the word.🥰 Now create the back of the card. (meaning or description) 🤗";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->message->getChat()->getId(),
            'reply_to_message_id' => $this->message->getMessageId(),
        ];

        $dbTlgParam = [
            'type' => TelegramModel::TYPE['CALLBACK_QUERY'],
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
            'reminder_type' => 'front',
            'user_id' => $this->message->getUserId()
        ];

        $reminderFrontParam = [
            'user_id' => $dbTlgParam['user_id'],
            'frontend' => $dbTlgParam['text'],
        ];

        DB::beginTransaction();
        try {
            $this->telegramRepository->create($dbTlgParam);
            $this->reminderRepository->create($reminderFrontParam);
            DB::commit();
            return $this->channel->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' - '. $exception->getFile() . ' - '. $exception->getLine());
            //todo throw exception
        }
    }
}
