<?php

namespace App\Application\Actions\Onboarding;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Domain\Onboarding\Dtos\SellerPaymentInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerPaymentInformationRepositoryInterface;
use App\Infrastructure\Models\SellerPaymentInformation;

class CreateSellerPaymentInformation
{
    public function __construct(
        private readonly SellerPaymentInformationRepositoryInterface $sellerPaymentInformationRepository,
    ) {}

    public function execute(SellerPaymentInformationDto $sellerPaymentInformationDto): SellerPaymentInformation
    {
        $this->validatePaymentInformation($sellerPaymentInformationDto);

        return $this->sellerPaymentInformationRepository->create($sellerPaymentInformationDto);
    }

    private function validatePaymentInformation(SellerPaymentInformationDto $sellerPaymentInformationDto): void
    {
        $existingSellerPaymentInformation = $this->sellerPaymentInformationRepository->findOtherPayment(
            $sellerPaymentInformationDto->getAccountNumber(),
            $sellerPaymentInformationDto->getBankCode(),
            $sellerPaymentInformationDto->getUserId(),
        );

        throw_if(
            $existingSellerPaymentInformation,
            ConflictHttpException::class,
            'Payment information exist for another seller'
        );
    }
}
