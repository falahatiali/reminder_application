<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\CallBackQueryDVO;
use App\DVO\Message\MessageDVO;
use App\Helpers\SocialChannelContract;
use App\Models\ReminderModel;
use App\Models\TelegramModel;
use App\Models\User;
use App\Service\Contracts\CreateBotCommandsContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateFront implements CreateBotCommandsContract
{
    public function __construct(private SocialChannelContract $channel, private MessageDVO $message)
    {
    }

    public function create()
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
            $front = TelegramModel::query()->create($dbTlgParam);
            $reminder = ReminderModel::query()->create($reminderFrontParam);
            DB::commit();
            return $this->channel->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            //todo throw exception
        }
    }
}
