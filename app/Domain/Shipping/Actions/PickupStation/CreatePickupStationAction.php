<?php

namespace App\Domain\Shipping\Actions\PickupStation;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationDto;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Resources\PickupStation\PickupStationResource;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;

class CreatePickupStationAction
{
    public function __construct(
        private readonly PickupStationRepositoryInterface $pickupStationRepository,
    ) {}

    public function execute(CreatePickupStationDto $createPickupStationDto): PickupStationResource
    {
        $pickupStation = $this->pickupStationRepository->findByColumn(
            PickupStation::class,
            'name',
            $createPickupStationDto->getName(),
        );

        throw_if($pickupStation, BadRequestException::class, 'Pickup station already exist');

        $pickupStation = $this->pickupStationRepository->store($createPickupStationDto);

        return new PickupStationResource($pickupStation);
    }
}
