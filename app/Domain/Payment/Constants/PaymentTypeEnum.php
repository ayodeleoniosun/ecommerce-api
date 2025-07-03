<?php

namespace App\Domain\Payment\Constants;

enum PaymentTypeEnum: string
{
    case CARD = 'card';
    case BANK_TRANSFER = 'bank_transfer';
    case PAY_ON_DELIVERY = 'pay_on_delivery';
}
