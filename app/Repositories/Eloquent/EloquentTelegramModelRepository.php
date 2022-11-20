<?php

namespace App\Repositories\Eloquent;

use App\Models\TelegramModel;
use App\Repositories\Contracts\TelegramRepositoryInterface;
use App\Repositories\RepositoryAbstract;

class EloquentTelegramModelRepository extends RepositoryAbstract implements TelegramRepositoryInterface
{
    public function entity(): string
    {
        return TelegramModel::class;
    }
}
