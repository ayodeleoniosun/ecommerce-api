<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;
use Illuminate\Http\UploadedFile;

class SellerBusinessInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $companyName,
        private readonly string $description,
        private readonly string $registrationNumber,
        private readonly string $taxIdentificationNumber,
        private UploadedFile|string|null $businessCertificatePath,
        private ?string $uuid = null,
    ) {}

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUUID(),
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

    public function getUUID(): ?string
    {
        return $this->uuid;
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

    public function getBusinessCertificatePath(): UploadedFile|string|null
    {
        return $this->businessCertificatePath;
    }

    public function setBusinessCertificatePath(string $path): void
    {
        $this->businessCertificatePath = $path;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}
