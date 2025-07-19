<?php

namespace App\Domain\Payment\Enums;

enum PaymentCategoryEnum: string
{
    case COLLECTION = 'collection';
    case DISBURSEMENT = 'disbursement';
}
