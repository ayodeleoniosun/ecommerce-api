<?php

namespace App\Domain\Shipping\Actions\ShippingAddress;

use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Domain\Shipping\Resources\ShippingAddress\CustomerShippingAddressResourceCollection;
use Illuminate\Http\Request;

class GetCustomerShippingAddresses
{
    public function __construct(
        private readonly CustomerShippingAddressRepositoryInterface $customerShippingAddressRepository,
    ) {}

    public function execute(Request $request): CustomerShippingAddressResourceCollection
    {
        $shippingAddresses = $this->customerShippingAddressRepository->index($request);

        return new CustomerShippingAddressResourceCollection($shippingAddresses);
    }
}
