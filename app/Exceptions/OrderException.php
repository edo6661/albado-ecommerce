<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    public function __construct(string $message = "Terjadi kesalahan pada order.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}

class OrderNotFoundException extends Exception
{
    public function __construct(string $message = "Order tidak ditemukan.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}

class OrderCannotBeCancelledException extends Exception
{
    public function __construct(string $message = "Order tidak dapat dibatalkan.", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}