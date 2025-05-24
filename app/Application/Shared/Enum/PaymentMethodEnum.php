<?php

namespace App\Application\Shared\Enum;

enum PaymentMethodEnum: string
{
    case CARD = 'card';
    case PAY_ON_DELIVERY = 'pay_on_delivery';
}
