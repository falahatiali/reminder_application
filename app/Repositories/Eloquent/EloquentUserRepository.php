<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\RepositoryAbstract;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository extends RepositoryAbstract implements UserRepositoryInterface
{
    public function entity(): string
    {
        return User::class;
    }

    public function createTelegramEntity(int $userId, array $properties)
    {
        return $this->find($userId)->telegramEntity()->create($properties);
    }
}
