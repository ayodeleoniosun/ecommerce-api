<?php

namespace App\Domain\Payment\Dtos\Card;

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

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            number: $data['number'],
            cvv: $data['cvv'],
            expiryMonth: $data['expiry_month'],
            expiryYear: $data['expiry_year'],
            pin: $data['pin']
        );
    }

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCvv(): string
    {
        return $this->cvv;
    }

    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    public function getPin(): string
    {
        return $this->pin;
    }
}
