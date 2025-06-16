<?php

namespace App\Enums;


enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'Pembeli',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ADMIN => 'Memiliki akses penuh ke sistem admin',
            self::USER => 'Dapat melakukan pembelian dan mengelola akun',
        };
    }

    public function hasAdminPrivileges(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageProducts(): bool
    {
        return in_array($this, [self::ADMIN, self::USER]);
    }
}