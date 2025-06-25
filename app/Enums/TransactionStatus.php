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
            self::PENDING => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Pembayaran Berhasil',
            self::CAPTURE => 'Pembayaran Ditangkap',
            self::DENY => 'Pembayaran Ditolak',
            self::CANCEL => 'Pembayaran Dibatalkan',
            self::EXPIRE => 'Pembayaran Kedaluwarsa',
            self::FAILURE => 'Pembayaran Gagal',
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
}