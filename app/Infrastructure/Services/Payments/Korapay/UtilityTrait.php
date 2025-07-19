<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;

trait UtilityTrait
{
    public static function getKorapayStatus(?string $status): string
    {
        if (! $status || $status === PaymentStatusEnum::PROCESSING->value) {
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

    public static function getAuthorizationData(string $authModel, array $data): array
    {
        if ($authModel === AuthModelEnum::OTP->value) {
            return self::getOtpAuthorizationData($data['otp']);
        }

        if ($authModel === AuthModelEnum::PIN->value) {
            return self::getPINAuthorizationData($data['pin']);
        }

        if ($authModel === AuthModelEnum::AVS->value) {
            return self::getAvsAuthorizationData($data['avs']);
        }

        return [];
    }

    private static function getOTPAuthorizationData(string $otp): array
    {
        return compact('otp');
    }

    private static function getPINAuthorizationData(string $pin): array
    {
        return compact('pin');
    }

    private static function getAVSAuthorizationData(array $avs): array
    {
        return [
            'avs' => [
                'address' => $avs['address'],
                'city' => $avs['city'],
                'country' => $avs['country'],
                'zip_code' => $avs['zip_code'],
                'state' => $avs['state'],
            ],
        ];
    }
}
