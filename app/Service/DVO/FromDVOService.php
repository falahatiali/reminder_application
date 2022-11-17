<?php

namespace App\Service\DVO;

use App\DVO\Message\FromDVO;

class FromDVOService
{
    public function create(array $from): FromDVO
    {
        return new FromDVO(
            $from['id'],
            $from['first_name'],
            $from['username'],
            $from['language_code'] ?? 'en',
            $from['is_bot'] ?? false);
    }
}
