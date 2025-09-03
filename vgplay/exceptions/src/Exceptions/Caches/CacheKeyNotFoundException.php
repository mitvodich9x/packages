<?php

namespace Vgplay\Exceptions\Exceptions\Caches;

use Vgplay\Exceptions\Exceptions\BaseException;

class CacheKeyNotFoundException extends BaseException
{
    public function __construct(string $key = '')
    {
        $message = $key
            ? "Không tìm thấy cache với khóa [{$key}]."
            : "Không tìm thấy cache.";

        parent::__construct(
            message: $message,
            httpCode: 404,
            errorKey: 'CACHE_KEY_NOT_FOUND',
            internalCode: 1001
        );
    }
}
