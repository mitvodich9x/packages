<?php

namespace Vgplay\Exceptions\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected string $errorKey;
    protected int $internalCode;

    public function __construct(
        string $message = '',
        int $httpCode = 500,
        string $errorKey = '',
        int $internalCode = 0
    ) {
        parent::__construct($message, $httpCode);
        $this->errorKey = $errorKey;
        $this->internalCode = $internalCode;
    }

    public function toArray(): array
    {
        return [
            'error' => $this->errorKey,
            'message' => $this->message,
            'code' => $this->getCode(),
            'internal_code' => $this->internalCode,
        ];
    }

    public function render($request)
    {
        return response()->json($this->toArray(), $this->getCode());
    }
}
