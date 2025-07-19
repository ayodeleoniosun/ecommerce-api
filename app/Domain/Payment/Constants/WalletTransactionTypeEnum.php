<?php

namespace App\Domain\Payment\Constants;

enum WalletTransactionTypeEnum: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
