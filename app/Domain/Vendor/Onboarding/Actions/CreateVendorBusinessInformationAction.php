<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Traits\FileUploadTrait;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorBusinessInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\Vendor\VendorBusinessInformation;
use Illuminate\Http\UploadedFile;

class CreateVendorBusinessInformationAction
{
    use FileUploadTrait, UtilitiesTrait;

    public function __construct(
        private readonly VendorBusinessInformationRepositoryInterface $vendorBusinessInformationRepository,
    ) {}

    public function execute(CreateVendorBusinessInformationDto $vendorBusinessDto): VendorBusinessInformation
    {
        $this->validateBusinessName($vendorBusinessDto);

        $this->validateBusinessRegistrationNumber($vendorBusinessDto);

        $this->validateBusinessTaxIdentificationNumber($vendorBusinessDto);

        $uuid = $this->getBusinessUUID($vendorBusinessDto);

        if ($vendorBusinessDto->getBusinessCertificatePath()) {
            $path = $this->uploadBusinessCertificate($vendorBusinessDto->getBusinessCertificatePath(), $uuid);

            $vendorBusinessDto->setBusinessCertificatePath($path);
        }

        $vendorBusinessDto->setUUID($uuid);

        return $this->vendorBusinessInformationRepository->create($vendorBusinessDto);
    }

    private function validateBusinessName(CreateVendorBusinessInformationDto $vendorBusinessDto): void
    {
        $existingVendorBusinessName = $this->vendorBusinessInformationRepository->findOtherBusiness(
            'name',
            $vendorBusinessDto->getCompanyName(),
            $vendorBusinessDto->getUserId(),
        );

        throw_if(
            $existingVendorBusinessName,
            ConflictHttpException::class,
            'Business name exist for another vendor',
        );
    }

    private function validateBusinessRegistrationNumber(CreateVendorBusinessInformationDto $vendorBusinessDto): void
    {
        $existingVendorRegistrationNumber = $this->vendorBusinessInformationRepository->findOtherBusiness(
            'registration_number',
            $vendorBusinessDto->getRegistrationNumber(),
            $vendorBusinessDto->getUserId(),
        );

        throw_if(
            $existingVendorRegistrationNumber,
            ConflictHttpException::class,
            'Registration number exist for another vendor',
        );
    }

    private function validateBusinessTaxIdentificationNumber(CreateVendorBusinessInformationDto $vendorBusinessDto,
    ): void {
        $existingVendorTaxIdentificationNumber = $this->vendorBusinessInformationRepository->findOtherBusiness(
            'tax_identification_number',
            $vendorBusinessDto->getTaxIdentificationNumber(),
            $vendorBusinessDto->getUserId(),
        );

        throw_if(
            $existingVendorTaxIdentificationNumber,
            ConflictHttpException::class,
            'Tax identification number exist for another vendor',
        );
    }

    private function getBusinessUUID(CreateVendorBusinessInformationDto $vendorBusinessDto): string
    {
        $business = $this->vendorBusinessInformationRepository->findBusiness('registration_number',
            $vendorBusinessDto->getRegistrationNumber());

        if (! $business) {
            return self::generateUUID();
        }

        return $business->uuid;
    }

    private function uploadBusinessCertificate(UploadedFile $file, string $uuid): string
    {
        $filename = 'vendors/business/certificates/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
