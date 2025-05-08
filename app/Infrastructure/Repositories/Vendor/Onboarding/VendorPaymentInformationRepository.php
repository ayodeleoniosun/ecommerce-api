<?php

namespace App\Infrastructure\Repositories\Vendor\Onboarding;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorPaymentInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\VendorPaymentInformation;

class VendorPaymentInformationRepository implements VendorPaymentInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return VendorPaymentInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateVendorPaymentInformationDto $paymentInformationDto): VendorPaymentInformation
    {
        return VendorPaymentInformation::updateOrCreate(
            ['user_id' => $paymentInformationDto->getUserId()],
            $paymentInformationDto->toArray(),
        );
    }

    public function findOtherPayment(string $accountNumber, string $bankCode, int $userId): ?VendorPaymentInformation
    {
        return VendorPaymentInformation::where('account_number', $accountNumber)
            ->where('bank_code', $bankCode)
            ->whereNot('user_id', $userId)->first();
    }

    public function findPayment(string $accountNumber, string $bankCode): ?VendorPaymentInformation
    {
        return VendorPaymentInformation::where('account_number', $accountNumber)
            ->where('bank_code', $bankCode)
            ->first();
    }
}
