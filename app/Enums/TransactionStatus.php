<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case CAPTURE = 'capture';
    case DENY = 'deny';
    case CANCEL = 'cancel';
    case EXPIRE = 'expire';
    case FAILURE = 'failure';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::SETTLEMENT => 'Berhasil',
            self::CAPTURE => 'Berhasil',
            self::DENY => 'Ditolak',
            self::CANCEL => 'Dibatalkan',
            self::EXPIRE => 'Kedaluwarsa',
            self::FAILURE => 'Gagal',
        };
    }

    public function isSuccess(): bool
    {
        return in_array($this, [self::SETTLEMENT, self::CAPTURE]);
    }

    public function isFailed(): bool
    {
        return in_array($this, [self::DENY, self::CANCEL, self::EXPIRE, self::FAILURE]);
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::SETTLEMENT, self::CAPTURE => 'success',
            self::DENY, self::CANCEL, self::EXPIRE, self::FAILURE => 'danger',
        };
    }
}