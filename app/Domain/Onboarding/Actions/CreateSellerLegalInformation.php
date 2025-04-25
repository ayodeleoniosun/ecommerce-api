<?php

namespace App\Domain\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Traits\FileUploadTrait;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Onboarding\Dtos\CreateSellerLegalInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Infrastructure\Models\SellerLegalInformation;
use Illuminate\Http\UploadedFile;

class CreateSellerLegalInformation
{
    use FileUploadTrait, UtilitiesTrait;

    public function __construct(
        private readonly SellerLegalInformationRepositoryInterface $sellerLegalInformationRepository,
    ) {}

    public function execute(CreateSellerLegalInformationDto $sellerLegalDto): SellerLegalInformation
    {
        $this->validateLegalEmail($sellerLegalDto);

        $uuid = $this->getLegalUUID($sellerLegalDto);

        if ($sellerLegalDto->getLegalCertificatePath()) {
            $path = $this->uploadLegalCertificate($sellerLegalDto->getLegalCertificatePath(), $uuid);

            $sellerLegalDto->setLegalCertificatePath($path);
        }

        $sellerLegalDto->setUUID($uuid);

        return $this->sellerLegalInformationRepository->create($sellerLegalDto);
    }

    private function validateLegalEmail(CreateSellerLegalInformationDto $sellerLegalDto): void
    {
        $existingSellerLegalEmail = $this->sellerLegalInformationRepository->findOtherLegal(
            'email',
            $sellerLegalDto->getEmail(),
            $sellerLegalDto->getUserId(),
        );

        throw_if(
            $existingSellerLegalEmail,
            ConflictHttpException::class,
            'Legal email address exist for another seller',
        );

    }

    private function getLegalUUID(CreateSellerLegalInformationDto $sellerLegalDto): string
    {
        $legal = $this->sellerLegalInformationRepository->findLegal('email', $sellerLegalDto->getEmail());

        if (! $legal) {
            return self::generateUUID();
        }

        return $legal->uuid;
    }

    private function uploadLegalCertificate(UploadedFile $file, string $uuid): string
    {
        $filename = 'sellers/legal/certificates/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
