<?php

namespace Vgplay\Exceptions\Exceptions\Games;

use Vgplay\Exceptions\Exceptions\BaseException;

class MissingCharacterConfiguration extends BaseException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'Lỗi chưa cài đặt cấu hình nhân vật');
    }
}
