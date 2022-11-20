<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Carbon\Carbon;

class Today implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->where('created_at','>=', Carbon::today());
    }
}
