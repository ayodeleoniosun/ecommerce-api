<?php

namespace App\Domain\Shipping\Dtos\PickupStation;

use App\Application\Shared\Traits\UtilitiesTrait;

class CreatePickupStationDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $countryUUID,
        private readonly string $stateUUID,
        private readonly string $cityUUID,
        private readonly int $countryId,
        private readonly int $stateId,
        private readonly int $cityId,
        private readonly string $name,
        private readonly string $address,
        private readonly string $contactPhoneNumber,
        private readonly string $contactName,

    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            countryUUID: $payload['country_id'],
            stateUUID: $payload['state_id'],
            cityUUID: $payload['city_id'],
            countryId: $payload['merged_country_id'],
            stateId: $payload['merged_state_id'],
            cityId: $payload['merged_city_id'],
            name: strtolower($payload['name']),
            address: strtolower($payload['address']),
            contactPhoneNumber: $payload['contact_phone_number'],
            contactName: strtolower($payload['contact_name'])
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'country_id' => $this->countryId,
            'state_id' => $this->stateId,
            'city_id' => $this->cityId,
            'name' => $this->name,
            'address' => $this->address,
            'contact_phone_number' => $this->contactPhoneNumber,
            'contact_name' => $this->contactName,
        ];
    }

    public function getCountryUUID(): string
    {
        return $this->countryUUID;
    }

    public function getStateUUID(): string
    {
        return $this->stateUUID;
    }

    public function getCityUUID(): string
    {
        return $this->cityUUID;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getStateId(): int
    {
        return $this->stateId;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhoneNumber(): string
    {
        return $this->contactPhoneNumber;
    }

    public function getContactName(): string
    {
        return $this->contactName;
    }
}
