<?php

namespace Vgplay\Exceptions\Exceptions\Items;

use Vgplay\Exceptions\Exceptions\BaseException;

class ItemNotFoundException extends BaseException
{
    public function __construct(string $message = "Không tìm thấy gói nạp", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
