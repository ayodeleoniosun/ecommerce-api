<?php

namespace App\Domain\Payment\Constants;

enum GatewayPrefixReference: string
{
    case KORAPAY = 'KPY-';
    case FLUTTERWAVE = 'FLW-';
    case PAYSTACK = 'PAY-';
    case STRIPE = 'STP-';
}
