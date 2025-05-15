<?php

namespace App\Domain\Shipping\Actions\PickupStation;

use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Resources\PickupStation\PickupStationResourceCollection;
use Illuminate\Http\Request;

class GetPickupStations
{
    public function __construct(
        private readonly PickupStationRepositoryInterface $pickupStationRepository,
    ) {}

    public function execute(Request $request,
    ): PickupStationResourceCollection {
        $pickupStations = $this->pickupStationRepository->index($request);

        return new PickupStationResourceCollection($pickupStations);

    }
}
