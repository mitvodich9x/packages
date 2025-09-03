<?php

namespace Vgplay\Exceptions\Exceptions\Payments;

use Vgplay\Exceptions\Exceptions\BaseException;

class PaymentNotFoundException extends BaseException
{
    public function __construct(string $message = "Không tìm thấy phương thức nạp", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
