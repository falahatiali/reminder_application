<?php

namespace App\Services\Telegram\Delete;

use App\Builders\Telegram\Query\Query;
use App\Helpers\SocialChannelContract;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\DeleteCommandContract;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteReminder implements DeleteCommandContract
{
    public function __construct(private Query                       $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private UserRepositoryInterface     $userRepository)
    {
    }

    public function action()
    {
        $text = explode(' ', $this->message->getMessage()->getText())[0];
        try {
            $user = $this->userRepository
                ->findWhere('telegram_id', '=', $this->message->getMessage()->getChat()->getId())
                ->first();

            $reminder = $this->reminderRepository
                ->where('user_id', '=', $user->id)
                ->findWhere('frontend', '=', trim($text))
                ->first();

            if ($reminder) {
                $this->reminderRepository->delete($reminder->id);
            }

            $response = "{$this->message->getMessage()->getChat()->getFirstName()}, {$text} deleted successfully";
            $parameters = [
                'text' => $response,
                'chat_id' => $this->message->getMessage()->getChat()->getId(),
                'reply_to_message_id' => $this->message->getMessage()->getMessageId(),
            ];

            return $this->channel->call('sendMessage', $parameters);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}
