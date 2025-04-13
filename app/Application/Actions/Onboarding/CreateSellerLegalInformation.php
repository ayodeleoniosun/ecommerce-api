<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerLegalInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Infrastructure\Models\SellerLegalInformation;

class CreateSellerLegalInformation
{
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

        return $this->sellerLegalInformationRepository->create($sellerLegalDto);
    }
}
