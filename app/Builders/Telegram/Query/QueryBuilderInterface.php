<?php

namespace App\Builders\Telegram\Query;

interface QueryBuilderInterface
{
    public function build(): Query;
}
