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

    public static function encryptCardData(string $paymentData, string $encryptionKey): string
    {
        $method = 'aes-256-gcm';
        $iv = openssl_random_pseudo_bytes(16);
        $tag = '';
        $cipherText = openssl_encrypt($paymentData, $method, $encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);

        return bin2hex($iv).':'.bin2hex($cipherText).':'.bin2hex($tag);
    }
}
