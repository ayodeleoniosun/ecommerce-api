<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;

class SellerContactInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $name,
        private readonly string $email,
        private readonly string $phoneNumber,
        private readonly string $country,
        private readonly string $state,
        private readonly string $city,
        private readonly string $address,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone_number' => $this->getPhoneNumber(),
            'country' => $this->getCountry(),
            'state' => $this->getState(),
            'city' => $this->getCity(),
            'address' => $this->getAddress(),
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now()->toDateTimeString(),
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
