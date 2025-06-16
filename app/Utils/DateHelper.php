<?php

use Carbon\Carbon;

class DateHelper
{
    public static function formatToHuman(string $date, string $format = 'd F Y'): string
    {
        return Carbon::parse($date)->translatedFormat($format);
    }

    public static function formatToHumanWithTime(string $date, string $format = 'd F Y H:i'): string
    {
        return Carbon::parse($date)->translatedFormat($format);
    }

    public static function diffForHumans(string $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }

    public static function isToday(string $date): bool
    {
        return Carbon::parse($date)->isToday();
    }

    public static function isYesterday(string $date): bool
    {
        return Carbon::parse($date)->isYesterday();
    }

    public static function isTomorrow(string $date): bool
    {
        return Carbon::parse($date)->isTomorrow();
    }

    public static function getAge(string $birthDate): int
    {
        return Carbon::parse($birthDate)->age;
    }

    public static function getStartOfDay(string $date = null): Carbon
    {
        return Carbon::parse($date ?? now())->startOfDay();
    }

    public static function getEndOfDay(string $date = null): Carbon
    {
        return Carbon::parse($date ?? now())->endOfDay();
    }
}