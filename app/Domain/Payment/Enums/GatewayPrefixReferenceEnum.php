<?php

namespace App\Domain\Payment\Enums;

enum GatewayPrefixReferenceEnum: string
{
    case KORAPAY = 'KPY-';
    case FLUTTERWAVE = 'FLW-';
    case PAYSTACK = 'PAY-';
    case STRIPE = 'STP-';

    public static function getPrefix($gateway): self
    {
        return collect(self::cases())
            ->first(fn ($case) => $case->name === strtoupper($gateway));
    }
}
