<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

class HasLive
{
    public function scopeLive(Builder $builder): Builder
    {
        return $builder->where("live", true);
    }
}
