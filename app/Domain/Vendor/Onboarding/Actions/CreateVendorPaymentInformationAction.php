<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorPaymentInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\Vendor\VendorPaymentInformation;

class CreateVendorPaymentInformationAction
{
    public function __construct(
        private readonly VendorPaymentInformationRepositoryInterface $vendorPaymentInformationRepository,
    ) {}

    public function execute(CreateVendorPaymentInformationDto $vendorPaymentInformationDto): VendorPaymentInformation
    {
        $this->validatePaymentInformation($vendorPaymentInformationDto);

        return $this->vendorPaymentInformationRepository->create($vendorPaymentInformationDto);
    }

    private function validatePaymentInformation(CreateVendorPaymentInformationDto $vendorPaymentInformationDto): void
    {
        $existingVendorPaymentInformation = $this->vendorPaymentInformationRepository->findOtherPayment(
            $vendorPaymentInformationDto->getAccountNumber(),
            $vendorPaymentInformationDto->getBankCode(),
            $vendorPaymentInformationDto->getUserId(),
        );

        throw_if(
            $existingVendorPaymentInformation,
            ConflictHttpException::class,
            'Payment information exist for another vendor',
        );
    }
}
