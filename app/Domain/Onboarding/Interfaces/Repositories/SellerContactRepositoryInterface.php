<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\SellerContactDto;
use App\Infrastructure\Models\SellerContact;

interface SellerContactRepositoryInterface
{
    public function create(SellerContactDto $sellerContactEntity): SellerContact;
}
