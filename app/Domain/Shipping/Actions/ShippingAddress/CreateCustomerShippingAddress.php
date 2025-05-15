<?php

namespace App\Domain\Shipping\Actions\ShippingAddress;

use App\Domain\Shipping\Dtos\ShippingAddress\CreateCustomerShippingAddressDto;
use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Domain\Shipping\Resources\ShippingAddress\CustomerShippingAddressResource;

class CreateCustomerShippingAddress
{
    public function __construct(
        private readonly CustomerShippingAddressRepositoryInterface $customerShippingAddressRepository,
    ) {}

    public function execute(CreateCustomerShippingAddressDto $createCustomerShippingAddressDto,
    ): CustomerShippingAddressResource {
        $shippingAddress = $this->customerShippingAddressRepository->store($createCustomerShippingAddressDto);

        return new CustomerShippingAddressResource($shippingAddress);
    }
}
