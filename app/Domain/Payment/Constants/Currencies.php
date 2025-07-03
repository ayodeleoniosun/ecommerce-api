<?php

namespace App\Domain\Payment\Constants;

enum Currencies: string
{
    case NGN = 'NGN';
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
}
