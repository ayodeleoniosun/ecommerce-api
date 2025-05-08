<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorContactInformationDto;
use App\Infrastructure\Models\VendorContactInformation;

interface VendorContactInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateVendorContactInformationDto $contactInformationDto): VendorContactInformation;

    public function findContact(string $field, string $value): ?VendorContactInformation;

    public function findOtherContact(string $field, string $value, int $userId): ?VendorContactInformation;
}
