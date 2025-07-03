<?php

namespace App\Domain\Payment\Dtos;

class CardData
{
    public function __construct(
        private readonly string $name,
        private readonly string $number,
        private readonly string $cvv,
        private readonly string $expiryMonth,
        private readonly string $expiryYear,
        private readonly string $pin,
    ) {}

    public static function toArray(self $data): array
    {
        return [
            'number' => $data->number,
            'cvv' => $data->cvv,
            'expiry_month' => $data->expiryMonth,
            'expiry_year' => $data->expiryYear,
            'name' => $data->name,
            'pin' => $data->pin,
        ];
    }
}
