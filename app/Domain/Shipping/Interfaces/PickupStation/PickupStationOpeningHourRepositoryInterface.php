<?php

namespace App\Domain\Shipping\Interfaces\PickupStation;

use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationOpeningHourDto;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStationOpeningHour;

interface PickupStationOpeningHourRepositoryInterface
{
    public function store(CreatePickupStationOpeningHourDto $createPickupStationOpeningHourDto,
    ): PickupStationOpeningHour;

    public function findExistingOpeningHour(
        int $pickupStationId,
        string $day,
    ): ?PickupStationOpeningHour;
}
