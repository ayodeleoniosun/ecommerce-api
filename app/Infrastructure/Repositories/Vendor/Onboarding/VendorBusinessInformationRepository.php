<?php

namespace App\Infrastructure\Repositories\Vendor\Onboarding;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorBusinessInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorBusinessInformationRepositoryInterface;
use App\Infrastructure\Models\VendorBusinessInformation;

class VendorBusinessInformationRepository implements VendorBusinessInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return VendorBusinessInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateVendorBusinessInformationDto $businessInformationDto): VendorBusinessInformation
    {
        return VendorBusinessInformation::updateOrCreate(
            ['user_id' => $businessInformationDto->getUserId()],
            $businessInformationDto->toArray(),
        );
    }

    public function findOtherBusiness(string $field, string $value, int $userId): ?VendorBusinessInformation
    {
        return VendorBusinessInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findBusiness(string $field, string $value): ?VendorBusinessInformation
    {
        return VendorBusinessInformation::where($field, $value)->first();
    }
}
