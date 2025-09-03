<?php

namespace Vgplay\Exceptions\Exceptions\Users;
use Vgplay\Exceptions\Exceptions\BaseException;

class UserNotFoundException extends BaseException
{
    public function __construct(string $message = "Người dùng không tồn tại", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
