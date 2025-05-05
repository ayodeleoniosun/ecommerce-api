<?php

namespace App\Domain\Vendor\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;
use App\Domain\Vendor\Onboarding\Requests\SellerContactInformationRequest;

class CreateSellerContactInformationDto
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

    public static function fromRequest(SellerContactInformationRequest $request): self
    {
        return new self(
            userId: $request->user_id,
            name: $request->contact_name,
            email: $request->contact_email,
            phoneNumber: $request->contact_phone_number,
            country: $request->country,
            state: $request->state,
            city: $request->city,
            address: $request->address
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'address' => $this->address,
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
