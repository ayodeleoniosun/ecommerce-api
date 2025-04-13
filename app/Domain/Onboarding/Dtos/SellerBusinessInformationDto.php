<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;

class SellerBusinessInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $companyName,
        private readonly string $description,
        private readonly string $registrationNumber,
        private readonly string $taxIdentificationNumber,
        private readonly string $businessCertificatePath,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'name' => $this->getCompanyName(),
            'description' => $this->getDescription(),
            'registration_number' => $this->getRegistrationNumber(),
            'tax_identification_number' => $this->getTaxIdentificationNumber(),
            'certificate_path' => $this->getBusinessCertificatePath(),
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now()->toDateTimeString(),
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getTaxIdentificationNumber(): string
    {
        return $this->taxIdentificationNumber;
    }

    public function getBusinessCertificatePath(): string
    {
        return $this->businessCertificatePath;
    }
}
