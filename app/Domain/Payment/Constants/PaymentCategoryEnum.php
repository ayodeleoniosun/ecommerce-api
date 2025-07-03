<?php

namespace App\Domain\Payment\Constants;

enum PaymentCategoryEnum: string
{
    case COLLECTION = 'collection';
    case DISBURSEMENT = 'disbursement';
}
