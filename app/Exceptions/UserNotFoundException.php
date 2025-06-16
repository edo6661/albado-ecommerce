<?php
namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'User tidak ditemukan.';
    
    public function __construct(?string $message = null, ?int $code = 404, ?Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}



