<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Infrastructure\Services\Payments\Flutterwave\Enum\AuthorizationModeEnum;
use Exception;

trait Utility
{
    /**
     * @throws Exception
     */
    public static function encryptCardData(array $data, string $key): string
    {
        if (strlen($key) !== 24) {
            throw new Exception('Key must be 24 bytes for 3DES encryption.');
        }

        $jsonData = json_encode($data);
        $blockSize = 8;
        $pad = $blockSize - (strlen($jsonData) % $blockSize);
        $paddedData = $jsonData.str_repeat(chr($pad), $pad);

        $encrypted = openssl_encrypt(
            $paddedData,
            'des-ede3-ecb', // 3DES-ECB algorithm
            $key,
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
        );

        if ($encrypted === false) {
            throw new Exception('Encryption failed.');
        }

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
