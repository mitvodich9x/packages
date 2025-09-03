<?php

namespace Vgplay\Exceptions\Exceptions\Caches;

use Vgplay\Exceptions\Exceptions\BaseException;

class CacheInvalidDataException extends BaseException
{
    public function __construct(string $key = '')
    {
        $message = $key
            ? "Dữ liệu cache không hợp lệ cho khóa [{$key}]."
            : "Dữ liệu cache không hợp lệ.";

        parent::__construct(
            message: $message,
            httpCode: 400,
            errorKey: 'CACHE_INVALID_DATA',
            internalCode: 1004
        );
    }
}
