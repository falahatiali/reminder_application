<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function createTelegramEntity(int $userId, array $properties);
}
