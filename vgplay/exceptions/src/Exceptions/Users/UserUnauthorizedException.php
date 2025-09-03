<?php

namespace Vgplay\Exceptions\Exceptions\Users;
use Vgplay\Exceptions\Exceptions\BaseException;

class UserUnauthorizedException extends BaseException
{
    public function __construct(string $message = "Lỗi xác thực người dùng", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}