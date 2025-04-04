<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;

class SellerContactDto
{
    public int $user_id;

    public function __construct(
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'address' => $this->address,
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now(),
        ];
    }
}
