<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Traits\FileUploadTrait;
use App\Domain\Onboarding\Dtos\SellerBusinessInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\SellerBusinessInformation;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;

class CreateSellerBusinessInformation
{
    use FileUploadTrait;

    public function __construct(
        private readonly SellerBusinessInformationRepositoryInterface $sellerBusinessInformationRepository,
    ) {}

    public function execute(SellerBusinessInformationDto $sellerBusinessDto): SellerBusinessInformation
    {
        $existingSellerBusinessName = $this->sellerBusinessInformationRepository->findOtherBusiness(
            'name',
            $sellerBusinessDto->getCompanyName(),
            $sellerBusinessDto->getUserId(),
        );

        throw_if($existingSellerBusinessName, ConflictHttpException::class,
            'Business name exist for another seller');

        $existingSellerRegistrationNumber = $this->sellerBusinessInformationRepository->findOtherBusiness(
            'registration_number',
            $sellerBusinessDto->getRegistrationNumber(),
            $sellerBusinessDto->getUserId(),
        );

        throw_if($existingSellerRegistrationNumber, ConflictHttpException::class,
            'Registration number exist for another seller');

        $existingSellerTaxIdentificationNumber = $this->sellerBusinessInformationRepository->findOtherBusiness(
            'tax_identification_number',
            $sellerBusinessDto->getTaxIdentificationNumber(),
            $sellerBusinessDto->getUserId(),
        );

        throw_if($existingSellerTaxIdentificationNumber, ConflictHttpException::class,
            'Tax identification number exist for another seller');

        $business = $this->sellerBusinessInformationRepository->findBusiness('registration_number',
            $sellerBusinessDto->getRegistrationNumber());

        $uuid = (! $business) ? Uuid::uuid4()->toString() : $business->uuid;

        if ($sellerBusinessDto->getBusinessCertificatePath()) {
            $path = $this->uploadBusinessCertificate($sellerBusinessDto->getBusinessCertificatePath(), $uuid);

            $sellerBusinessDto->setBusinessCertificatePath($path);
        }

        $sellerBusinessDto->setUUID($uuid);

        return $this->sellerBusinessInformationRepository->create($sellerBusinessDto);
    }

    public function uploadBusinessCertificate(UploadedFile $file, string $uuid): string
    {
        $filename = 'sellers/business/certificates/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
