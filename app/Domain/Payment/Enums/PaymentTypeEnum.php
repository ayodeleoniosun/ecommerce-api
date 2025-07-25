<?php

namespace App\Domain\Payment\Enums;

enum PaymentTypeEnum: string
{
    case CARD = 'card';
    case WALLET = 'wallet';
    case PAY_ON_DELIVERY = 'pay_on_delivery';
}
