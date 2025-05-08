<?php

namespace App\Domain\Vendor\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;
use App\Domain\Vendor\Onboarding\Requests\VendorBusinessInformationRequest;
use Illuminate\Http\UploadedFile;

class CreateVendorBusinessInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $companyName,
        private readonly string $description,
        private readonly string $registrationNumber,
        private readonly string $taxIdentificationNumber,
        private UploadedFile|string|null $businessCertificatePath = null,
        private ?string $uuid = null,
    ) {}

    public static function fromRequest(VendorBusinessInformationRequest $request): self
    {
        return new self(
            userId: $request->user_id,
            companyName: $request->company_name,
            description: $request->description,
            registrationNumber: $request->registration_number,
            taxIdentificationNumber: $request->tax_identification_number,
            businessCertificatePath: $request->certificate_path
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'user_id' => $this->userId,
            'name' => $this->companyName,
            'description' => $this->description,
            'registration_number' => $this->registrationNumber,
            'tax_identification_number' => $this->taxIdentificationNumber,
            'certificate_path' => $this->businessCertificatePath,
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
