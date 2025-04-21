<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerContactInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Infrastructure\Models\SellerContactInformation;

class CreateSellerContactInformation
{
    public function __construct(
        private readonly SellerContactInformationRepositoryInterface $sellerContactInformationRepository,
    ) {}

    public function execute(SellerContactInformationDto $sellerContactDto): SellerContactInformation
    {
        $this->validateContactEmail($sellerContactDto);

        $this->validateContactPhoneNumber($sellerContactDto);

        return $this->sellerContactInformationRepository->create($sellerContactDto);
    }

    private function validateContactEmail(SellerContactInformationDto $sellerContactDto): void
    {
        $existingSellerEmail = $this->sellerContactInformationRepository->findOtherContact(
            'email',
            $sellerContactDto->getEmail(),
            $sellerContactDto->getUserId(),
        );

        throw_if(
            $existingSellerEmail,
            ConflictHttpException::class,
            'Email address exist for another seller'
        );
    }

    private function validateContactPhoneNumber(SellerContactInformationDto $sellerContactDto): void
    {
        $existingSellerPhoneNumber = $this->sellerContactInformationRepository->findOtherContact(
            'phone_number',
            $sellerContactDto->getPhoneNumber(),
            $sellerContactDto->getUserId(),
        );

        throw_if(
            $existingSellerPhoneNumber,
            ConflictHttpException::class,
            'Phone number exist for another seller'
        );
    }
}
