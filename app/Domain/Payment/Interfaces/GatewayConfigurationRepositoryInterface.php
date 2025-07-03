<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Infrastructure\Models\Payment\GatewayConfiguration;

interface GatewayConfigurationRepositoryInterface
{
    public function findGatewayConfiguration(GatewayFilterData $filterData): ?GatewayConfiguration;
}
