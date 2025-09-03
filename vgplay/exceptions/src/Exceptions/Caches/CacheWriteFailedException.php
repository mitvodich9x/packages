<?php

namespace Vgplay\Exceptions\Exceptions\Caches;

use Vgplay\Exceptions\Exceptions\BaseException;

class CacheWriteFailedException extends BaseException
{
    public function __construct(string $key = '')
    {
        $message = $key
            ? "Không thể ghi cache với khóa [{$key}]."
            : "Không thể ghi dữ liệu vào cache.";

        parent::__construct(
            message: $message,
            httpCode: 500,
            errorKey: 'CACHE_WRITE_FAILED',
            internalCode: 1003
        );
    }
}
