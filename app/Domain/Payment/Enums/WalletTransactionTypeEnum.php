<?php

namespace App\Domain\Payment\Enums;

enum WalletTransactionTypeEnum: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
