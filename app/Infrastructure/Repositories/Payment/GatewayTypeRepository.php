<?php

namespace App\Infrastructure\Repositories\Payment;

use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Payment\GatewayType;

class GatewayTypeRepository implements GatewayTypeRepositoryInterface
{
    public function findGatewayType(GatewayFilterData $filterData): ?GatewayType
    {
        return GatewayType::query()
            ->where('type', $filterData->getType())
            ->where('category', $filterData->getCategory())
            ->where('currency', $filterData->getCurrency())
            ->first();
    }
}
