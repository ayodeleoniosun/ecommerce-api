<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\CreateSellerLegalInformationDto;
use App\Infrastructure\Models\SellerLegalInformation;

interface SellerLegalInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool;

    public function create(CreateSellerLegalInformationDto $legalInformationDto): SellerLegalInformation;

    public function findLegal(string $field, string $value): ?SellerLegalInformation;

    public function findOtherLegal(string $field, string $value, int $userId): ?SellerLegalInformation;
}
