<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Domain\Vendor\Onboarding\Interfaces\SellerBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\SellerPaymentInformationRepositoryInterface;

class GetSellerSetupStatus
{
    public function __construct(
        private readonly SellerContactInformationRepositoryInterface $contactInformationRepository,
        private readonly SellerBusinessInformationRepositoryInterface $businessInformationRepository,
        private readonly SellerPaymentInformationRepositoryInterface $paymentInformationRepository,
        private readonly SellerLegalInformationRepositoryInterface $legalInformationRepository,
    ) {}

    public function execute(int $userId): array
    {
        return [
            'completed_contact_information' => $this->contactInformationRepository->isCompleted($userId),
            'completed_business_information' => $this->businessInformationRepository->isCompleted($userId),
            'completed_payment_information' => $this->paymentInformationRepository->isCompleted($userId),
            'completed_legal_information' => $this->legalInformationRepository->isCompleted($userId),
        ];
    }
}
