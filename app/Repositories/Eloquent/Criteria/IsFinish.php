<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class IsFinish implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->finish();
    }
}
