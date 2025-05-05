<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateSellerPaymentInformationDto;
use App\Infrastructure\Models\SellerPaymentInformation;

interface SellerPaymentInformationRepositoryInterface
{
    public function create(CreateSellerPaymentInformationDto $paymentInformationDto): SellerPaymentInformation;

    public function findPayment(string $accountNumber, string $bankCode): ?SellerPaymentInformation;

    public function findOtherPayment(string $accountNumber, string $bankCode, int $userId): ?SellerPaymentInformation;

    public function isCompleted(int $userId): bool;
}
