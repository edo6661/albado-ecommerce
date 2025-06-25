<?php

namespace App\Enums;

enum PaymentType: string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
    case ECHANNEL = 'echannel';
    case GOPAY = 'gopay';
    case SHOPEEPAY = 'shopeepay';
    case QRIS = 'qris';
    case CSTORE = 'cstore';
    case AKULAKU = 'akulaku';
    case BCA_KLIKPAY = 'bca_klikpay';
    case BCA_KLIKBCA = 'bca_klikbca';
    case BRI_EPAY = 'bri_epay';
    case CIMB_CLICKS = 'cimb_clicks';
    case DANAMON_ONLINE = 'danamon_online';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Kartu Kredit',
            self::BANK_TRANSFER => 'Transfer Bank',
            self::ECHANNEL => 'Mandiri Bill',
            self::GOPAY => 'GoPay',
            self::SHOPEEPAY => 'ShopeePay',
            self::QRIS => 'QRIS',
            self::CSTORE => 'Convenience Store',
            self::AKULAKU => 'Akulaku',
            self::BCA_KLIKPAY => 'BCA KlikPay',
            self::BCA_KLIKBCA => 'BCA KlikBCA',
            self::BRI_EPAY => 'BRI ePay',
            self::CIMB_CLICKS => 'CIMB Clicks',
            self::DANAMON_ONLINE => 'Danamon Online Banking',
            self::OTHER => 'Lainnya',
        };
    }

    public function isEwallet(): bool
    {
        return in_array($this, [self::GOPAY, self::SHOPEEPAY]);
    }

    public function isBankTransfer(): bool
    {
        return $this === self::BANK_TRANSFER;
    }
}
