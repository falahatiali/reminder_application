<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class IsActive implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->active();
    }
}
