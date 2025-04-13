<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;

class SellerLegalInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $fullName,
        private readonly string $email,
        private readonly string $legalCertificatePath,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'fullname' => $this->getFullName(),
            'email' => $this->getEmail(),
            'certificate_path' => $this->getLegalCertificatePath(),
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now()->toDateTimeString(),
        ];
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

    public function getLegalCertificatePath(): string
    {
        return $this->legalCertificatePath;
    }
}
