<?php

namespace Vgplay\Exceptions\Exceptions\Games;

use Vgplay\Exceptions\Exceptions\BaseException;

class GameNotFoundException extends BaseException
{
    public function __construct($message = 'Không tìm thấy game tương ứng.')
    {
        parent::__construct($message);
        parent::__construct(
            message: $message,
            httpCode: 404,
            errorKey: 'GAME_NOT_FOUND',
            internalCode: 2001
        );
    }
}
