<?php

namespace App\Domain\Shipping\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Shipping\Actions\PickupStation\CreatePickupStationAction;
use App\Domain\Shipping\Actions\PickupStation\CreatePickupStationOpeningHourAction;
use App\Domain\Shipping\Actions\PickupStation\GetPickupStationAction;
use App\Domain\Shipping\Actions\PickupStation\GetPickupStationsAction;
use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationDto;
use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationOpeningHourDto;
use App\Domain\Shipping\Requests\PickupStation\CreatePickupStationOpeningHourRequest;
use App\Domain\Shipping\Requests\PickupStation\CreatePickupStationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PickupStationController
{
    public function __construct(
        private readonly GetPickupStationsAction $pickupStations,
        private readonly GetPickupStationAction $pickupStation,
        private readonly CreatePickupStationAction $createPickupStation,
        private readonly CreatePickupStationOpeningHourAction $createPickupStationOpeningHour,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->pickupStations->execute($request);

            return ApiResponse::success('Pickup stations successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function view(string $pickupStationUUID): JsonResponse
    {
        try {
            $data = $this->pickupStation->execute($pickupStationUUID);

            return ApiResponse::success('Pickup station successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(CreatePickupStationRequest $request): JsonResponse
    {
        $pickupStationDto = CreatePickupStationDto::fromRequest($request->validated());

        try {
            $data = $this->createPickupStation->execute($pickupStationDto);

            return ApiResponse::success('Pickup station successfully created', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function storeOpeningHours(CreatePickupStationOpeningHourRequest $request): JsonResponse
    {
        $pickupStationOpeningHourDto = CreatePickupStationOpeningHourDto::fromRequest($request->validated());

        try {
            $data = $this->createPickupStationOpeningHour->execute($pickupStationOpeningHourDto);

            return ApiResponse::success('Pickup station opening hour successfully created', $data,
                Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
