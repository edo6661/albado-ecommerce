<?php
namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $message = 'Autentikasi gagal.';
    
    public function __construct(?string $message, int $code = 401, ?Exception $previous)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}