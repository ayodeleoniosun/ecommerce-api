<?php

namespace App\Domain\Vendor\Onboarding\Dtos;

use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Vendor\Onboarding\Requests\VendorPaymentInformationRequest;

class CreateVendorPaymentInformationDto
{
    public function __construct(
        private readonly int $userId,
        private readonly string $accountName,
        private readonly string $accountNumber,
        private readonly string $bankCode,
        private readonly string $bankName,
        private readonly string $swiftCode,
    ) {}

    public static function fromRequest(VendorPaymentInformationRequest $request): self
    {
        return new self(
            userId: $request->user_id,
            accountName: $request->account_name,
            accountNumber: $request->account_number,
            bankCode: $request->bank_code,
            bankName: $request->bank_name,
            swiftCode: $request->swift_code
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'account_name' => $this->accountName,
            'account_number' => $this->accountNumber,
            'bank_code' => $this->bankCode,
            'bank_name' => $this->bankName,
            'swift_code' => $this->swiftCode,
            'status' => UserStatusEnum::ACTIVE->value,
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
