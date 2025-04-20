<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Traits\FileUploadTrait;
use App\Domain\Onboarding\Dtos\SellerLegalInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Infrastructure\Models\SellerLegalInformation;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;

class CreateSellerLegalInformation
{
    use FileUploadTrait;

    public function __construct(
        private readonly SellerLegalInformationRepositoryInterface $sellerLegalInformationRepository,
    ) {}

    public function execute(SellerLegalInformationDto $sellerLegalDto): SellerLegalInformation
    {
        $existingSellerLegalEmail = $this->sellerLegalInformationRepository->findOtherLegal(
            'email',
            $sellerLegalDto->getEmail(),
            $sellerLegalDto->getUserId(),
        );

        throw_if($existingSellerLegalEmail, ConflictHttpException::class,
            'Legal email address exist for another seller');

        $legal = $this->sellerLegalInformationRepository->findLegal('email', $sellerLegalDto->getEmail());

        $uuid = (! $legal) ? Uuid::uuid4() : $legal->uuid;

        if ($sellerLegalDto->getLegalCertificatePath()) {
            $path = $this->uploadLegalCertificate($sellerLegalDto->getLegalCertificatePath(), $uuid);

            $sellerLegalDto->setLegalCertificatePath($path);
        }

        $sellerLegalDto->setUUID($uuid);

        return $this->sellerLegalInformationRepository->create($sellerLegalDto);
    }

    public function uploadLegalCertificate(UploadedFile $file, string $uuid): string
    {
        $filename = 'sellers/legal/certificates/'.$uuid.'.jpg';

        return $this->uploadFile($file, $filename, 'public');
    }
}
