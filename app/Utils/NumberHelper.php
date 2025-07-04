<?php

namespace App\Utils;

use Illuminate\Support\Str;

class NumberHelper
{
    public function formatIDRAmount($amount): int
    {
        return (int) round($amount);
    }
}