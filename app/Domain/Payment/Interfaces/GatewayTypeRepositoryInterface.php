<?php

namespace App\Domain\Payment\Interfaces;

use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Infrastructure\Models\Payment\GatewayType;

interface GatewayTypeRepositoryInterface
{
    public function findGatewayType(GatewayFilterData $filterData): ?GatewayType;
}
