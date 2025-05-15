<?php

namespace App\Infrastructure\Repositories\Shipping\PickupStation;

use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationOpeningHourDto;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationOpeningHourRepositoryInterface;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStationOpeningHour;
use App\Infrastructure\Repositories\BaseRepository;

class PickupStationOpeningHourRepository extends BaseRepository implements PickupStationOpeningHourRepositoryInterface
{
    public function store(CreatePickupStationOpeningHourDto $createPickupStationOpeningHourDto,
    ): PickupStationOpeningHour {
        return PickupStationOpeningHour::create($createPickupStationOpeningHourDto->toArray());
    }

    public function findExistingOpeningHour(int $pickupStationId, string $day): ?PickupStationOpeningHour
    {
        return PickupStationOpeningHour::where('pickup_station_id', $pickupStationId)
            ->where('day_of_week', $day)
            ->first();
    }
}
