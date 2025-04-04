<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\Dtos\SellerContactDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactRepositoryInterface;
use App\Infrastructure\Models\SellerContact;

class SellerContactRepository implements SellerContactRepositoryInterface
{
    public function create(SellerContactDto $sellerContactDto): SellerContact
    {
        return SellerContact::updateOrCreate(
            ['user_id' => $sellerContactDto->user_id],
            $sellerContactDto->toArray(),
        );
    }
}
