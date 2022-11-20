<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ByUser implements CriterionInterface
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function apply($entity)
    {
        return $entity->whereUserId($this->userId);
    }
}
