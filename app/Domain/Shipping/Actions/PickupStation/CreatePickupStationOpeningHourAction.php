<?php

namespace App\Domain\Shipping\Actions\PickupStation;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationOpeningHourDto;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationOpeningHourRepositoryInterface;
use App\Domain\Shipping\Resources\PickupStation\PickupStationOpeningHourResource;

class CreatePickupStationOpeningHourAction
{
    public function __construct(
        private readonly PickupStationOpeningHourRepositoryInterface $pickupStationOpeningHourRepository,
    ) {}

    public function execute(CreatePickupStationOpeningHourDto $createPickupStationOpeningHourDto,
    ): PickupStationOpeningHourResource {
        $pickupStationOpeningHour = $this->pickupStationOpeningHourRepository->findExistingOpeningHour(
            $createPickupStationOpeningHourDto->getPickupStationId(),
            $createPickupStationOpeningHourDto->getDayOfWeek());

        throw_if($pickupStationOpeningHour, BadRequestException::class, 'Pickup station opening hour already exist');

        $pickupStationOpeningHour = $this->pickupStationOpeningHourRepository->store($createPickupStationOpeningHourDto);

        return new PickupStationOpeningHourResource($pickupStationOpeningHour);
    }
}
