<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\SellerContactInformationDto;
use App\Infrastructure\Models\SellerContactInformation;

interface SellerContactInformationRepositoryInterface
{
    public function create(SellerContactInformationDto $contactInformationDto): SellerContactInformation;

    public function findContact(string $field, string $value): ?SellerContactInformation;

    public function findOtherContact(string $field, string $value, int $userId): ?SellerContactInformation;
}
