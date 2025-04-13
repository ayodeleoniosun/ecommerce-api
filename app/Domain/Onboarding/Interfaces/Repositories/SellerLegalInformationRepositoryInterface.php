<?php

namespace App\Domain\Onboarding\Interfaces\Repositories;

use App\Domain\Onboarding\Dtos\SellerLegalInformationDto;
use App\Infrastructure\Models\SellerLegalInformation;

interface SellerLegalInformationRepositoryInterface
{
    public function create(SellerLegalInformationDto $legalInformationDto): SellerLegalInformation;

    public function findLegal(string $field, string $value): ?SellerLegalInformation;

    public function findOtherLegal(string $field, string $value, int $userId): ?SellerLegalInformation;
}
