<?php

namespace App\Infrastructure\Repositories\Vendor\Onboarding;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorContactInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorContactInformationRepositoryInterface;
use App\Infrastructure\Models\Vendor\VendorContactInformation;

class VendorContactInformationRepository implements VendorContactInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return VendorContactInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateVendorContactInformationDto $contactInformationDto): VendorContactInformation
    {
        return VendorContactInformation::updateOrCreate(
            ['user_id' => $contactInformationDto->getUserId()],
            $contactInformationDto->toArray(),
        );
    }

    public function findOtherContact(string $field, string $value, int $userId): ?VendorContactInformation
    {
        return VendorContactInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findContact(string $field, string $value): ?VendorContactInformation
    {
        return VendorContactInformation::where($field, $value)->first();
    }
}
