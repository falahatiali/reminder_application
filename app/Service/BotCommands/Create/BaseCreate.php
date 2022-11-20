<?php

namespace App\Service\BotCommands\Create;

use App\Helpers\SocialChannelContract;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

abstract class BaseCreate
{
    protected SocialChannelContract $channel;
    protected TelegramRepositoryInterface $telegramRepository;
    protected ReminderRepositoryInterface $reminderRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        SocialChannelContract       $channel,
        TelegramRepositoryInterface $telegramRepository,
        ReminderRepositoryInterface $reminderRepository,
        UserRepositoryInterface     $userRepository
    )
    {
        $this->channel = $channel;
        $this->telegramRepository = $telegramRepository;
        $this->reminderRepository = $reminderRepository;
        $this->userRepository = $userRepository;
    }
}
