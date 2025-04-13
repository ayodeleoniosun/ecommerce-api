<?php

namespace App\Domain\Onboarding\Dtos;

use App\Application\Shared\Enum\UserEnum;

class SellerPaymentInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $accountName,
        private readonly string $accountNumber,
        private readonly string $bankCode,
        private readonly string $bankName,
        private readonly string $swiftCode,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'account_name' => $this->getAccountName(),
            'account_number' => $this->getAccountNumber(),
            'bank_code' => $this->getBankCode(),
            'bank_name' => $this->getBankName(),
            'swift_code' => $this->getSwiftCode(),
            'status' => UserEnum::ACTIVE->value,
            'verified_at' => now()->toDateTimeString(),
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function getSwiftCode(): string
    {
        return $this->swiftCode;
    }
}
