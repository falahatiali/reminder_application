<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class IsNotFinish implements CriterionInterface
{

    public function apply($entity)
    {
        return $entity->notFinish();
    }
}
