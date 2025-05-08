<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorPaymentInformationDto;
use App\Infrastructure\Models\VendorPaymentInformation;

interface VendorPaymentInformationRepositoryInterface
{
    public function create(CreateVendorPaymentInformationDto $paymentInformationDto): VendorPaymentInformation;

    public function findPayment(string $accountNumber, string $bankCode): ?VendorPaymentInformation;

    public function findOtherPayment(string $accountNumber, string $bankCode, int $userId): ?VendorPaymentInformation;

    public function isCompleted(int $userId): bool;
}
