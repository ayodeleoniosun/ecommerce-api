<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BaseService
{
    public function http(): PendingRequest
    {
        return Http::timeout(60)
            ->withHeaders($this->headers())
            ->baseUrl(config('payment.kora.base_url'));
    }

    public function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.config('payment.kora.secret_key'),
        ];
    }

    public function encryptCardData(string $paymentData): string
    {
        $encryptionKey = config('payment.kora.encryption_key');
        $method = 'aes-256-gcm';
        $iv = openssl_random_pseudo_bytes(16);
        $tag = '';
        $cipherText = openssl_encrypt($paymentData, $method, $encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);

        return bin2hex($iv).':'.bin2hex($cipherText).':'.bin2hex($tag);
    }
}
