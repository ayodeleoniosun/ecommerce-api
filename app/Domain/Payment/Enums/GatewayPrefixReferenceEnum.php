<?php

namespace App\Domain\Payment\Enums;

enum GatewayPrefixReferenceEnum: string
{
    case KORAPAY = 'KPY-';
    case FLUTTERWAVE = 'FLW-';
    case PAYSTACK = 'PAY-';
    case STRIPE = 'STP-';
}
