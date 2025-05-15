<?php

namespace App\Domain\Shipping\Interfaces\PickupStation;

use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationDto;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface PickupStationRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator;

    public function store(CreatePickupStationDto $createPickupStationDto): PickupStation;
}
