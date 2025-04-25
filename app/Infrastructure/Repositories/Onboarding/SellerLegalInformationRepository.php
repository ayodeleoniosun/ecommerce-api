<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\Dtos\CreateSellerLegalInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerLegalInformationRepositoryInterface;
use App\Infrastructure\Models\SellerLegalInformation;

class SellerLegalInformationRepository implements SellerLegalInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return SellerLegalInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateSellerLegalInformationDto $legalInformationDto): SellerLegalInformation
    {
        return SellerLegalInformation::updateOrCreate(
            ['user_id' => $legalInformationDto->getUserId()],
            $legalInformationDto->toArray(),
        );
    }

    public function findOtherLegal(string $field, string $value, int $userId): ?SellerLegalInformation
    {
        return SellerLegalInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findLegal(string $field, string $value): ?SellerLegalInformation
    {
        return SellerLegalInformation::where($field, $value)->first();
    }
}
