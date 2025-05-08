<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorBusinessInformationDto;
use App\Infrastructure\Models\VendorBusinessInformation;

interface VendorBusinessInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateVendorBusinessInformationDto $businessInformationDto): VendorBusinessInformation;

    public function findBusiness(string $field, string $value): ?VendorBusinessInformation;

    public function findOtherBusiness(string $field, string $value, int $userId): ?VendorBusinessInformation;
}
