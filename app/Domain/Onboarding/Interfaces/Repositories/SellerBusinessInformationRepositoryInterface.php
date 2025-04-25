<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\CreateSellerBusinessInformationDto;
use App\Infrastructure\Models\SellerBusinessInformation;

interface SellerBusinessInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateSellerBusinessInformationDto $businessInformationDto): SellerBusinessInformation;

    public function findBusiness(string $field, string $value): ?SellerBusinessInformation;

    public function findOtherBusiness(string $field, string $value, int $userId): ?SellerBusinessInformation;
}
