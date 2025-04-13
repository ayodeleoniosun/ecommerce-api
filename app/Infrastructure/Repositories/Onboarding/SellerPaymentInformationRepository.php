<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\Dtos\SellerPaymentInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\SellerPaymentInformation;

class SellerPaymentInformationRepository implements SellerPaymentInformationRepositoryInterface
{
    public function create(SellerPaymentInformationDto $paymentInformationDto): SellerPaymentInformation
    {
        return SellerPaymentInformation::updateOrCreate(
            ['user_id' => $paymentInformationDto->getUserId()],
            $paymentInformationDto->toArray(),
        );
    }

    public function findOtherPayment(string $accountNumber, string $bankCode, int $userId): ?SellerPaymentInformation
    {
        return SellerPaymentInformation::where('account_number', $accountNumber)
            ->where('bank_code', $bankCode)
            ->whereNot('user_id', $userId)->first();
    }

    public function findPayment(string $accountNumber, string $bankCode): ?SellerPaymentInformation
    {
        return SellerPaymentInformation::where('account_number', $accountNumber)
            ->where('bank_code', $bankCode)
            ->first();
    }
}
