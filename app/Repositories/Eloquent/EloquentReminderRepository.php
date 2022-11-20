<?php

namespace App\Repositories\Eloquent;

use App\Models\ReminderModel;
use App\Repositories\Contracts\ReminderRepositoryInterface;
use App\Repositories\RepositoryAbstract;

class EloquentReminderRepository extends RepositoryAbstract implements ReminderRepositoryInterface
{
    public function entity(): string
    {
        return ReminderModel::class;
    }
}
