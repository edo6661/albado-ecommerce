<?php
namespace App\Exceptions;

use Exception;

class ProfileNotFoundException extends Exception
{
    protected $message = 'Profile tidak ditemukan.';
    
    public function __construct(?string $message, int $code = 404, ?Exception $previous)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}

