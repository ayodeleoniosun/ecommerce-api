<?php

namespace App\Domain\Vendor\Onboarding\Dtos;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Domain\Vendor\Onboarding\Requests\VendorLegalInformationRequest;
use Illuminate\Http\UploadedFile;

class CreateVendorLegalInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $fullName,
        private readonly string $email,
        private UploadedFile|string|null $legalCertificatePath = null,
        private ?string $uuid = null,
    ) {}

    public static function fromRequest(VendorLegalInformationRequest $request): self
    {
        return new self(
            userId: $request->user_id,
            fullName: $request->fullname,
            email: $request->email,
            legalCertificatePath: $request->legal_certificate_path
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'user_id' => $this->userId,
            'fullname' => $this->fullName,
            'email' => $this->email,
            'certificate_path' => $this->legalCertificatePath,
            'status' => UserStatusEnum::ACTIVE->value,
            'verified_at' => now()->toDateTimeString(),
        ];
    }

    public function getLegalCertificatePath(): UploadedFile|string|null
    {
        return $this->legalCertificatePath;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getUUID(): ?string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setLegalCertificatePath(string $path): void
    {
        $this->legalCertificatePath = $path;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}
