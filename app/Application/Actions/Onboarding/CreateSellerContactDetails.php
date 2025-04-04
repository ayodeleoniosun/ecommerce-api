<?php

namespace App\Application\Actions\Onboarding;

use App\Domain\Onboarding\Dtos\SellerContactDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactRepositoryInterface;
use App\Infrastructure\Models\SellerContact;

class CreateSellerContactDetails
{
    public function __construct(private readonly SellerContactRepositoryInterface $sellerContactRepository) {}

    public function execute(SellerContactDto $sellerContactDto): SellerContact
    {
        $sellerContactDto->user_id = auth()->user()->id;

        return $this->sellerContactRepository->create($sellerContactDto);
    }
}
