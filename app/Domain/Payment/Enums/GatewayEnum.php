<?php

namespace App\Domain\Payment\Enums;

enum GatewayEnum: string
{
    case KORAPAY = 'korapay';
    case FLUTTERWAVE = 'flutterwave';
    case PAYSTACK = 'paystack';
    case STRIPE = 'stripe';
}
