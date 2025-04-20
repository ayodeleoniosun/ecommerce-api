<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;
use Illuminate\Http\UploadedFile;

class SellerLegalInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $fullName,
        private readonly string $email,
        private UploadedFile|string|null $legalCertificatePath,
        private ?string $uuid = null,
    ) {}

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUUID(),
            'user_id' => $this->getUserId(),
            'fullname' => $this->getFullName(),
            'email' => $this->getEmail(),
            'certificate_path' => $this->getLegalCertificatePath(),
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

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLegalCertificatePath(): UploadedFile|string|null
    {
        return $this->legalCertificatePath;
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
