<?php

namespace App\Infrastructure\Repositories\Payment;

use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Interfaces\GatewayConfigurationRepositoryInterface;
use App\Infrastructure\Models\Payment\GatewayConfiguration;
use App\Infrastructure\Repositories\BaseRepository;

class GatewayConfigurationRepository extends BaseRepository implements GatewayConfigurationRepositoryInterface
{
    public function findGatewayConfiguration(GatewayFilterData $filterData): ?GatewayConfiguration
    {
        return GatewayConfiguration::query()
            ->where('type', $filterData->getType())
            ->where('category', $filterData->getCategory())
            ->where('currency', $filterData->getCurrency())
            ->where('gateway_id', $filterData->getGatewayId())
            ->first();
    }
}
