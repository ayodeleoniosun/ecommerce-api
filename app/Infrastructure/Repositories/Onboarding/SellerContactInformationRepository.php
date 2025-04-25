<?php

namespace App\Infrastructure\Repositories\Onboarding;

use App\Domain\Onboarding\Dtos\CreateSellerContactInformationDto;
use App\Domain\Onboarding\Interfaces\Repositories\SellerContactInformationRepositoryInterface;
use App\Infrastructure\Models\SellerContactInformation;

class SellerContactInformationRepository implements SellerContactInformationRepositoryInterface
{
    public function isCompleted(int $userId): bool
    {
        return SellerContactInformation::where('user_id', $userId)->exists();
    }

    public function create(CreateSellerContactInformationDto $contactInformationDto): SellerContactInformation
    {
        return SellerContactInformation::updateOrCreate(
            ['user_id' => $contactInformationDto->getUserId()],
            $contactInformationDto->toArray(),
        );
    }

    public function findOtherContact(string $field, string $value, int $userId): ?SellerContactInformation
    {
        return SellerContactInformation::where($field, $value)
            ->whereNot('user_id', $userId)->first();
    }

    public function findContact(string $field, string $value): ?SellerContactInformation
    {
        return SellerContactInformation::where($field, $value)->first();
    }
}
