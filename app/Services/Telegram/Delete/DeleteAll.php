<?php

namespace App\Services\Telegram\Delete;

use App\Builders\Telegram\Message\Message;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Contracts\DeleteCommandContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteAll implements DeleteCommandContract
{
    public function __construct(private Message                     $message,
                                private SocialChannelContract       $channel,
                                private ReminderRepositoryInterface $reminderRepository,
                                private UserRepositoryInterface     $userRepository)
    {
    }

    public function action()
    {
        $response = "{$this->message->getChat()->getFirstName()}, All your reminders deleted successfully.";

        try {
            $user = $this->userRepository
                ->findWhere('telegram_id', '=', $this->message->getChat()->getId())
                ->first();

            $reminders = $this->reminderRepository
                ->findWhere('user_id', '=', $user->id)
                ->get();

            Log::error($reminders);

            $parameters = [
                'text' => $response,
                'chat_id' => $this->message->getChat()->getId(),
                'reply_to_message_id' => $this->message->getMessageId(),
            ];

            return $this->channel->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage() . ' - ' . $exception->getFile() . ' - ' . $exception->getLine());
            //todo
        }
    }
}
