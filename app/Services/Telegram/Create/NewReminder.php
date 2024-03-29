<?php

namespace App\Services\Telegram\Create;

use App\Builders\Telegram\Query\Query;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\CreateBotCommandContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewReminder implements CreateBotCommandContract
{
    public function __construct(
        private SocialChannelContract   $channel,
        private Query        $message,
        private UserRepositoryInterface $userRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getMessage()->getChat()->getFirstName()}, Please send your word!";

        $parameters = [
            'text' => $response,
            'chat_id' => $this->message->getMessage()->getChat()->getId(),
            'reply_to_message_id' => $this->message->getMessage()->getMessageId(),
        ];

        DB::beginTransaction();
        try {
            $user = $this->userRepository
                ->findWhere('telegram_id', $this->message->getMessage()->getChat()->getId())
                ->first();

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
                'chat_instance' => $this->message->getChatInstance(),
                'data' => $this->message->getData(),
                'telegram' => $this->message->toArray(),
                'reminder_type' => 'create_new',
                'user_id' => $user->id
            ];

            $this->userRepository->createTelegramEntity($user->id, $dbTlgParam);

            DB::commit();
            return $this->channel->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' - '. $exception->getFile() . ' - '. $exception->getLine());
            // todo - return exception
        }
    }
}
