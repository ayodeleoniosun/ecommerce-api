<?php

namespace App\Domain\Vendor\Onboarding\Interfaces;

use App\Domain\Vendor\Onboarding\Dtos\CreateSellerContactInformationDto;
use App\Infrastructure\Models\SellerContactInformation;

interface SellerContactInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateSellerContactInformationDto $contactInformationDto): SellerContactInformation;

    public function findContact(string $field, string $value): ?SellerContactInformation;

    public function findOtherContact(string $field, string $value, int $userId): ?SellerContactInformation;
}
