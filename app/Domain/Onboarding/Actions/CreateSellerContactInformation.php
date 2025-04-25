<?php

namespace App\Domain\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\CreateSellerContactInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Infrastructure\Models\SellerContactInformation;

class CreateSellerContactInformation
{
    public function __construct(
        private readonly SellerContactInformationRepositoryInterface $sellerContactInformationRepository,
    ) {}

    public function execute(CreateSellerContactInformationDto $sellerContactDto): SellerContactInformation
    {
        $this->validateContactEmail($sellerContactDto);

        $this->validateContactPhoneNumber($sellerContactDto);

        return $this->sellerContactInformationRepository->create($sellerContactDto);
    }

    private function validateContactEmail(CreateSellerContactInformationDto $sellerContactDto): void
    {
        $existingSellerEmail = $this->sellerContactInformationRepository->findOtherContact(
            'email',
            $sellerContactDto->getEmail(),
            $sellerContactDto->getUserId(),
        );

        throw_if(
            $existingSellerEmail,
            ConflictHttpException::class,
            'Email address exist for another seller',
        );
    }

    private function validateContactPhoneNumber(CreateSellerContactInformationDto $sellerContactDto): void
    {
        $existingSellerPhoneNumber = $this->sellerContactInformationRepository->findOtherContact(
            'phone_number',
            $sellerContactDto->getPhoneNumber(),
            $sellerContactDto->getUserId(),
        );

        throw_if(
            $existingSellerPhoneNumber,
            ConflictHttpException::class,
            'Phone number exist for another seller',
        );
    }
}
