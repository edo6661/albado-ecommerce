<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Sudah Dibayar',
            self::PROCESSING => 'Sedang Diproses',
            self::SHIPPED => 'Sedang Dikirim',
            self::DELIVERED => 'Sudah Diterima',
            self::CANCELLED => 'Dibatalkan',
            self::FAILED => 'Gagal',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'info',
            self::PROCESSING => 'primary',
            self::SHIPPED => 'secondary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
            self::FAILED => 'danger',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::PAID]);
    }
}


