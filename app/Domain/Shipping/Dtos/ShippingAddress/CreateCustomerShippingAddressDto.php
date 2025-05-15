<?php

namespace App\Domain\Shipping\Dtos\ShippingAddress;

use App\Application\Shared\Enum\AddressTypeEnum;
use App\Application\Shared\Traits\UtilitiesTrait;

class CreateCustomerShippingAddressDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $firstname,
        private readonly string $lastname,
        private readonly string $phoneNumber,
        private readonly string $countryUUID,
        private readonly string $stateUUID,
        private readonly string $cityUUID,
        private readonly int $countryId,
        private readonly int $stateId,
        private readonly int $cityId,
        private readonly string $address,
        private readonly ?string $additionalNote,
        private readonly ?bool $default,

    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            firstname: $payload['firstname'],
            lastname: $payload['lastname'],
            phoneNumber: $payload['phone_number'],
            countryUUID: $payload['country_id'],
            stateUUID: $payload['state_id'],
            cityUUID: $payload['city_id'],
            countryId: $payload['merged_country_id'],
            stateId: $payload['merged_state_id'],
            cityId: $payload['merged_city_id'],
            address: strtolower($payload['address']),
            additionalNote: $payload['additional_note'] ?? null,
            default: $payload['default'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'firstname' => strtolower($this->firstname),
            'lastname' => strtolower($this->lastname),
            'phone_number' => $this->phoneNumber,
            'user_id' => auth()->user()->id,
            'country_id' => $this->countryId,
            'state_id' => $this->stateId,
            'city_id' => $this->cityId,
            'address' => $this->address,
            'additional_note' => $this->additionalNote,
            'status' => $this->default ? AddressTypeEnum::DEFAULT->value : AddressTypeEnum::OTHERS->value,
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAdditionalNote(): ?string
    {
        return $this->additionalNote;
    }
}
