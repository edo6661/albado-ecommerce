<?php

namespace App\Exceptions;

use Exception;
class TransactionException extends Exception
{
    protected $message = 'Terjadi kesalahan pada transaksi.';
}
class TransactionNotFoundException extends TransactionException
{
    public function __construct(string $message = "Transaksi tidak ditemukan.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}