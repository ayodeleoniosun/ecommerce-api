<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\Dtos\CreateSellerBusinessInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\SellerBusinessInformation;

class SellerBusinessInformationRepository implements SellerBusinessInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return SellerBusinessInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateSellerBusinessInformationDto $businessInformationDto): SellerBusinessInformation
    {
        return SellerBusinessInformation::updateOrCreate(
            ['user_id' => $businessInformationDto->getUserId()],
            $businessInformationDto->toArray(),
        );
    }

    public function findOtherBusiness(string $field, string $value, int $userId): ?SellerBusinessInformation
    {
        return SellerBusinessInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findBusiness(string $field, string $value): ?SellerBusinessInformation
    {
        return SellerBusinessInformation::where($field, $value)->first();
    }
}
