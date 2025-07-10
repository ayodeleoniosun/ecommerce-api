<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorContactInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Infrastructure\Models\Vendor\VendorContactInformation;

class CreateVendorContactInformationAction
{
    public function __construct(
        private readonly VendorContactInformationRepositoryInterface $vendorContactInformationRepository,
    ) {}

    public function execute(CreateVendorContactInformationDto $vendorContactDto): VendorContactInformation
    {
        $this->validateContactEmail($vendorContactDto);

        $this->validateContactPhoneNumber($vendorContactDto);

        return $this->vendorContactInformationRepository->create($vendorContactDto);
    }

    private function validateContactEmail(CreateVendorContactInformationDto $vendorContactDto): void
    {
        $existingVendorEmail = $this->vendorContactInformationRepository->findOtherContact(
            'email',
            $vendorContactDto->getEmail(),
            $vendorContactDto->getUserId(),
        );

        throw_if(
            $existingVendorEmail,
            ConflictHttpException::class,
            'Email address exist for another vendor',
        );
    }

    private function validateContactPhoneNumber(CreateVendorContactInformationDto $vendorContactDto): void
    {
        $existingVendorPhoneNumber = $this->vendorContactInformationRepository->findOtherContact(
            'phone_number',
            $vendorContactDto->getPhoneNumber(),
            $vendorContactDto->getUserId(),
        );

        throw_if(
            $existingVendorPhoneNumber,
            ConflictHttpException::class,
            'Phone number exist for another vendor',
        );
    }
}
