<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerBusinessInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\SellerBusinessInformation;

class CreateSellerBusinessInformation
{
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

        return $this->sellerBusinessInformationRepository->create($sellerBusinessDto);
    }
}
