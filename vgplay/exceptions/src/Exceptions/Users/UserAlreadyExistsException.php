<?php

namespace Vgplay\Exceptions\Exceptions\Users;
use Vgplay\Exceptions\Exceptions\BaseException;

class UserAlreadyExistsException extends BaseException
{
    public function __construct(string $message = "Nguời dùng đã tồn tại trong hệ thống", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}