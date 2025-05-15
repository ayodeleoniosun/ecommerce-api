<?php

namespace App\Domain\Shipping\Interfaces\ShippingAddress;

use App\Domain\Shipping\Dtos\ShippingAddress\CreateCustomerShippingAddressDto;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;

interface CustomerShippingAddressRepositoryInterface
{
    public function store(CreateCustomerShippingAddressDto $createCustomerShippingAddressDto): CustomerShippingAddress;
}
