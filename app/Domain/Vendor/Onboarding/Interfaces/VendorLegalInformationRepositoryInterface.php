<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateVendorLegalInformationDto;
use App\Infrastructure\Models\Vendor\VendorLegalInformation;

interface VendorLegalInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateVendorLegalInformationDto $legalInformationDto): VendorLegalInformation;

    public function findLegal(string $field, string $value): ?VendorLegalInformation;

    public function findOtherLegal(string $field, string $value, int $userId): ?VendorLegalInformation;
}
