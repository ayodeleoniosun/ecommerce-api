<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

trait Utility
{
    public static function encryptCardData(string $encryptionKey, string $paymentData): string
    {
        $encrypted = openssl_encrypt($paymentData, 'DES-EDE3', $encryptionKey, OPENSSL_RAW_DATA);

        return base64_encode($encrypted);
    }
}
