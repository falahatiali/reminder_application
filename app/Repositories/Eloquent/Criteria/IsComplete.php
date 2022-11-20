<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class IsComplete implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->isComplete();
    }
}
