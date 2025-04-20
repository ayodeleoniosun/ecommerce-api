<?php

namespace App\Application\Actions\Onboarding;

use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;

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
