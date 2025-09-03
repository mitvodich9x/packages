<?php

namespace Vgplay\Exceptions\Exceptions\Apis;

use Vgplay\Exceptions\Exceptions\BaseException;

class VgpApiException extends BaseException
{
    public function __construct($message = "", $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        ], $this->getCode());
    }
}
