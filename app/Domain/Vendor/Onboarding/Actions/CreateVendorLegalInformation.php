<?php

namespace App\Domain\Vendor\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Traits\FileUploadTrait;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Vendor\Onboarding\Dtos\CreateVendorLegalInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Infrastructure\Models\VendorLegalInformation;
use Illuminate\Http\UploadedFile;

class CreateVendorLegalInformation
{
    use FileUploadTrait, UtilitiesTrait;

    public function __construct(
        private readonly VendorLegalInformationRepositoryInterface $vendorLegalInformationRepository,
    ) {}

    public function execute(CreateVendorLegalInformationDto $vendorLegalDto): VendorLegalInformation
    {
        $this->validateLegalEmail($vendorLegalDto);

        $uuid = $this->getLegalUUID($vendorLegalDto);

        if ($vendorLegalDto->getLegalCertificatePath()) {
            $path = $this->uploadLegalCertificate($vendorLegalDto->getLegalCertificatePath(), $uuid);

            $vendorLegalDto->setLegalCertificatePath($path);
        }

        $vendorLegalDto->setUUID($uuid);

        return $this->vendorLegalInformationRepository->create($vendorLegalDto);
    }

    private function validateLegalEmail(CreateVendorLegalInformationDto $vendorLegalDto): void
    {
        $existingVendorLegalEmail = $this->vendorLegalInformationRepository->findOtherLegal(
            'email',
            $vendorLegalDto->getEmail(),
            $vendorLegalDto->getUserId(),
        );

        throw_if(
            $existingVendorLegalEmail,
            ConflictHttpException::class,
            'Legal email address exist for another vendor',
        );

    }

    private function getLegalUUID(CreateVendorLegalInformationDto $vendorLegalDto): string
    {
        $legal = $this->vendorLegalInformationRepository->findLegal('email', $vendorLegalDto->getEmail());

        if (! $legal) {
            return self::generateUUID();
        }

        return $legal->uuid;
    }

    private function uploadLegalCertificate(UploadedFile $file, string $uuid): string
    {
        $filename = 'vendors/legal/certificates/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
