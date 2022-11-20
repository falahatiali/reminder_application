<?php

namespace App\Service\BotCommands\Create;

use App\DVO\Message\CallBackQueryDVO;
use App\Helpers\Date;
use App\Helpers\SocialChannelContract;
use App\Models\TelegramModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Eloquent\Criteria\IsNotComplete;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Scheduler\MyCronExpression;
use App\Service\Contracts\CreateBotCommandsContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateFrequency implements CreateBotCommandsContract
{
    public function __construct(
        private CallBackQueryDVO            $data,
        private SocialChannelContract       $channel,
        private TelegramRepositoryInterface $telegramRepository,
        private ReminderRepositoryInterface $reminderRepository)
    {
    }

    public function create()
    {
        $response = "{$this->data->getMessage()->getChat()->getFirstName()}, Almost done 🤗";

        $parameters = [
            'chat_id' => $this->data->getMessage()->getChat()->getId(),
            'text' => $response,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $this->data->getMessage()->getMessageId(),
        ];

        DB::beginTransaction();
        try {

            $dbTlgParam = [
                'type' => TelegramModel::TYPE['MESSAGE'],
                'from_id' => $this->data->getMessage()->getFrom()->getId(),
                'message_id' => $this->data->getMessage()->getMessageId(),
                'is_bot' => $this->data->getMessage()->getFrom()->isBot(),
                'first_name' => $this->data->getMessage()->getChat()->getFirstName(),
                'username' => $this->data->getMessage()->getChat()->getUsername() ?? '',
                'language_code' => $this->data->getFrom()->getLanguageCode() ?? '',
                'chat_id' => $this->data->getMessage()->getChat()->getId(),
                'chat_type' => $this->data->getMessage()->getChat()->getType(),
                'unix_timestamp' => $this->data->getMessage()->getDate(),
                'text' => $this->data->getMessage()->getText(),
                'telegram' => $this->data->toArray(),
                'reminder_type' => 'frequency',
                'finish' => true,
                'user_id' => $this->data->getMessage()->getUserId()
            ];

            $this->telegramRepository->create($dbTlgParam);

            $value = Date::allMappings()[$this->data->getData()];
            $expression = new MyCronExpression($value);
            if (!is_array($expression) && !is_string($expression)) {
                $expression = $expression->getParts();
                $expression = implode(' ', $expression);
            }

            $this->reminderRepository->withCriteria(new IsNotComplete(), new LatestFirst())
                ->updateWhere('user_id', '=', $dbTlgParam['user_id'], [
                    'expression' => $expression,
                    'frequency' => $this->data->getData(),
                    'is_complete' => true
                ]);

            DB::commit();

            return $this->channel->call('sendMessage', $parameters);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            //todo
        }
    }
}
