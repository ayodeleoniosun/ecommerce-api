<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;

class GetVendorSetupStatus
{
    public function __construct(
        private readonly VendorContactInformationRepositoryInterface $contactInformationRepository,
        private readonly VendorBusinessInformationRepositoryInterface $businessInformationRepository,
        private readonly VendorPaymentInformationRepositoryInterface $paymentInformationRepository,
        private readonly VendorLegalInformationRepositoryInterface $legalInformationRepository,
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
