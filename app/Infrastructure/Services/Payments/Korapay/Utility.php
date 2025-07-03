<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Domain\Payment\Constants\PaymentStatusEnum;

trait Utility
{
    public static function getKorapayStatus(string $status): string
    {
        if ($status === PaymentStatusEnum::PROCESSING->value) {
            return PaymentStatusEnum::FAILED->value;
        }

        return $status;
    }
}
