<?php

namespace App\Infrastructure\Repositories\Shipping\ShippingAddress;

use App\Domain\Shipping\Dtos\ShippingAddress\CreateCustomerShippingAddressDto;
use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CustomerShippingAddressRepository extends BaseRepository implements CustomerShippingAddressRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;
        $country = $request->input('country') ?? null;
        $state = $request->input('state') ?? null;
        $city = $request->input('city') ?? null;

        return CustomerShippingAddress::where('user_id', auth()->user()->id)
            ->with('country', 'state', 'city')
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
                $query->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('additional_note', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function store(CreateCustomerShippingAddressDto $createCustomerShippingAddressDto): CustomerShippingAddress
    {
        return CustomerShippingAddress::create($createCustomerShippingAddressDto->toArray());
    }
}
