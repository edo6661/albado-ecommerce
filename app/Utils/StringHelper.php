<?php

namespace App\Utils;

use Illuminate\Support\Str;

class StringHelper
{
    public static function generateRandomString(int $length = 10): string
    {
        return Str::random($length);
    }

    public static function generateSlug(string $title): string
    {
        return Str::slug($title);
    }

    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        return Str::limit($text, $length, $suffix);
    }

    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];
        
        $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        
        return $maskedName . '@' . $domain;
    }

    public static function maskPhone(string $phone): string
    {
        $length = strlen($phone);
        $visibleStart = 3;
        $visibleEnd = 3;
        
        if ($length <= $visibleStart + $visibleEnd) {
            return $phone;
        }
        
        return substr($phone, 0, $visibleStart) . 
               str_repeat('*', $length - $visibleStart - $visibleEnd) . 
               substr($phone, -$visibleEnd);
    }

    public static function formatCurrency(float $amount, string $currency = 'IDR'): string
    {
        return $currency . ' ' . number_format($amount, 0, ',', '.');
    }

    public static function formatNumber(float $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', '.');
    }
}