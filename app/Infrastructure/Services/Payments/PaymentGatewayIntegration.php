<?php

namespace App\Infrastructure\Services\Payments;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class PaymentGatewayIntegration
{
    public string $gateway;

    public string $baseUrl;

    public string $publicKey;

    public string $secretKey;

    public string $encryptionKey;

    public string $webhookURL;

    public function __construct()
    {
        $this->baseUrl = config("payment.gateways.{$this->gateway}.base_url");
        $this->publicKey = config("payment.gateways.{$this->gateway}.public_key");
        $this->secretKey = config("payment.gateways.{$this->gateway}.secret_key");
        $this->encryptionKey = config("payment.gateways.{$this->gateway}.encryption_key");
        $this->webhookURL = config("payment.gateways.{$this->gateway}.webhook_url");
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function request(string $method, string $path, array $payload = [], array $options = []): array
    {
        $url = $this->baseUrl.$path;

        $http = Http::timeout(60)->acceptJson();

        $authorization = Arr::get($options, 'authorization');

        if ($authorization) {
            $http->withHeaders([
                'Authorization' => 'Bearer '.$this->secretKey,
            ]);
        }

        $response = match (strtolower($method)) {
            'post' => $http->post($url, $payload),
            default => $http->get($url, $payload),
        };

        return $response->json();
    }
}
