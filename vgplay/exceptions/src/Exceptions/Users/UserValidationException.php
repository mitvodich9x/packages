<?php

namespace Vgplay\Exceptions\Exceptions\Users;

use Vgplay\Exceptions\Exceptions\BaseException;

class UserValidationException extends BaseException
{
    public function __construct(string $message = "User validation failed.", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
