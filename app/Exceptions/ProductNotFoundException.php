<?php
namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    protected $message = 'Product tidak ditemukan.';
    
    public function __construct(?string $message = null, ?int $code = 404, ?Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}



