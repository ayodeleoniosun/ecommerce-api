<?php

namespace App\Domain\Payment\Enums;

enum PaymentTypeEnum: string
{
    case CARD = 'card';
    case WALLET = 'wallet';
    case BANK_TRANSFER = 'bank_transfer';
    case PAY_ON_DELIVERY = 'pay_on_delivery';
}
