<?php

namespace App\Domain\Shipping\Actions\PickupStation;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Resources\PickupStation\PickupStationResource;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;

class GetPickupStation
{
    public function __construct(
        private readonly PickupStationRepositoryInterface $pickupStationRepository,
    ) {}

    public function execute(string $pickupStationUUID): PickupStationResource
    {
        $pickupStation = $this->pickupStationRepository->findByColumn(
            PickupStation::class,
            'uuid',
            $pickupStationUUID,
        );

        throw_if(! $pickupStation, ResourceNotFoundException::class, 'Pickup station does not exist');

        $pickupStation->load('country', 'state', 'city', 'openingHours');

        return new PickupStationResource($pickupStation);
    }
}
