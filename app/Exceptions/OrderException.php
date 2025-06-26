<?php

namespace App\Exceptions;

use Exception;

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