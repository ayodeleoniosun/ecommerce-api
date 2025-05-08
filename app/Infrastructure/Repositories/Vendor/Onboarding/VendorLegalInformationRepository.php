<?php

namespace App\Infrastructure\Repositories\Vendor\Onboarding;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorLegalInformationDto;
use App\Domain\Vendor\Onboarding\Interfaces\VendorLegalInformationRepositoryInterface;
use App\Infrastructure\Models\VendorLegalInformation;

class VendorLegalInformationRepository implements VendorLegalInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return VendorLegalInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateVendorLegalInformationDto $legalInformationDto): VendorLegalInformation
    {
        return VendorLegalInformation::updateOrCreate(
            ['user_id' => $legalInformationDto->getUserId()],
            $legalInformationDto->toArray(),
        );
    }

    public function findOtherLegal(string $field, string $value, int $userId): ?VendorLegalInformation
    {
        return VendorLegalInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findLegal(string $field, string $value): ?VendorLegalInformation
    {
        return VendorLegalInformation::where($field, $value)->first();
    }
}
