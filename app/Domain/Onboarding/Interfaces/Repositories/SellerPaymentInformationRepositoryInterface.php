<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\SellerPaymentInformationDto;
use App\Infrastructure\Models\SellerPaymentInformation;

interface SellerPaymentInformationRepositoryInterface
{
    public function create(SellerPaymentInformationDto $paymentInformationDto): SellerPaymentInformation;

    public function findPayment(string $accountNumber, string $bankCode): ?SellerPaymentInformation;

    public function findOtherPayment(string $accountNumber, string $bankCode, int $userId): ?SellerPaymentInformation;

    public function isCompleted(int $userId): bool;
}
