<?php

namespace App\Domain\Payment\Dtos;

class AvsAuthorizationData
{
    public function __construct(
        private readonly string $state,
        private readonly string $city,
        private readonly string $country,
        private readonly string $address,
        private readonly string $zipCode,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            state: $data['state'],
            city: $data['city'],
            country: $data['country'],
            address: $data['address'],
            zipCode: $data['zipCode'],
        );
    }

    public static function toArray(self $data): array
    {
        return [
            'state' => $data->state,
            'city' => $data->city,
            'country' => $data->country,
            'address' => $data->address,
            'zip_code' => $data->zipCode,
        ];
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }
}
