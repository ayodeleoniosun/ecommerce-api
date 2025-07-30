<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Infrastructure\Services\Payments\Flutterwave\Enum\AuthorizationModeEnum;

trait Utility
{
    public static function encryptCardData(array $data, string $key): string
    {
        $dataString = json_encode($data);

        // 3DES key must be 24 bytes (192 bits). Pad or truncate if necessary.
        $key = substr(hash('sha256', $key, true), 0, 24);

        $cipher = 'DES-EDE3'; // 3DES ECB mode in OpenSSL
        $options = OPENSSL_RAW_DATA | OPENSSL_NO_PADDING;

        // Pad the data to a multiple of 8 bytes (block size)
        $blockSize = 8;
        $padding = $blockSize - (strlen($dataString) % $blockSize);
        $dataString .= str_repeat(chr($padding), $padding);

        $encrypted = openssl_encrypt($dataString, $cipher, $key, $options);

        return base64_encode($encrypted);
    }

    public static function getFlutterwaveChargeStatus(?string $status): string
    {
        if ($status === PaymentStatusEnum::PENDING->value) {
            return PaymentStatusEnum::PROCESSING->value;
        }

        return $status;
    }

    public static function getFlutterwaveVerifyStatus(?string $status): string
    {
        if ($status === PaymentStatusEnum::SUCCESSFUL->value) {
            return PaymentStatusEnum::SUCCESS->value;
        }

        return $status;
    }

    public static function requiresRedirection(string $mode): bool
    {
        if ($mode === AuthorizationModeEnum::REDIRECT->value) {
            return true;
        }

        return false;
    }
}
