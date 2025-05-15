<?php

namespace App\Infrastructure\Repositories\Shipping\PickupStation;

use App\Domain\Shipping\Dtos\PickupStation\CreatePickupStationDto;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Infrastructure\Models\PickupStation\PickupStation;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PickupStationRepository extends BaseRepository implements PickupStationRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;
        $country = $request->input('country') ?? null;
        $state = $request->input('state') ?? null;
        $city = $request->input('city') ?? null;

        return PickupStation::with('country', 'state', 'city', 'openingHours')
            ->when($country, function ($query) use ($country) {
                $query->whereHas('country', function ($query) use ($country) {
                    $query->where('uuid', $country);
                });
            })->when($state, function ($query) use ($state) {
                $query->whereHas('state', function ($query) use ($state) {
                    $query->where('uuid', $state);
                });
            })->when($city, function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('uuid', $city);
                });
            })->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_phone_number', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function store(CreatePickupStationDto $createPickupStationDto): PickupStation
    {
        return PickupStation::create($createPickupStationDto->toArray());
    }
}
