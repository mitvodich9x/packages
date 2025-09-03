<?php

namespace Vgplay\Exceptions\Exceptions\Apis;

use Vgplay\Exceptions\Exceptions\BaseException;

class ApiRequestException extends BaseException
{
    protected mixed $context;

    public function __construct(string $message = "Lỗi lấy dữ liệu", int $code = 500, mixed $context = null)
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function getContext(): mixed
    {
        return $this->context;
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
