<?php

namespace Vgplay\Exceptions\Exceptions\Caches;

use Vgplay\Exceptions\Exceptions\BaseException;

class CacheConnectionFailedException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            message: "Không thể kết nối đến hệ thống cache Redis.",
            httpCode: 500,
            errorKey: "CACHE_CONNECTION_FAILED",
            internalCode: 1002
        );
    }
}