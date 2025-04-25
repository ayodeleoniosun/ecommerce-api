<?php

namespace App\Domain\Onboarding\Actions;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\CreateSellerPaymentInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\SellerPaymentInformation;

class CreateSellerPaymentInformation
{
    public function __construct(
        private readonly SellerPaymentInformationRepositoryInterface $sellerPaymentInformationRepository,
    ) {}

    public function execute(CreateSellerPaymentInformationDto $sellerPaymentInformationDto): SellerPaymentInformation
    {
        $this->validatePaymentInformation($sellerPaymentInformationDto);

        return $this->sellerPaymentInformationRepository->create($sellerPaymentInformationDto);
    }

    private function validatePaymentInformation(CreateSellerPaymentInformationDto $sellerPaymentInformationDto): void
    {
        $existingSellerPaymentInformation = $this->sellerPaymentInformationRepository->findOtherPayment(
            $sellerPaymentInformationDto->getAccountNumber(),
            $sellerPaymentInformationDto->getBankCode(),
            $sellerPaymentInformationDto->getUserId(),
        );

        throw_if(
            $existingSellerPaymentInformation,
            ConflictHttpException::class,
            'Payment information exist for another seller',
        );
    }
}
