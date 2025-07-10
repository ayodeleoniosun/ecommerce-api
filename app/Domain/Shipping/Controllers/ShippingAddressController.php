<?php

namespace App\Domain\Shipping\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Shipping\Actions\ShippingAddress\CreateCustomerShippingAddressAction;
use App\Domain\Shipping\Actions\ShippingAddress\GetCustomerShippingAddressesAction;
use App\Domain\Shipping\Dtos\ShippingAddress\CreateCustomerShippingAddressDto;
use App\Domain\Shipping\Requests\ShippingAddress\CreateCustomerShippingAddressRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShippingAddressController
{
    public function __construct(
        private readonly GetCustomerShippingAddressesAction $shippingAddresses,
        private readonly CreateCustomerShippingAddressAction $createCustomerShippingAddress,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->shippingAddresses->execute($request);

            return ApiResponse::success('Shipping addresses successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(CreateCustomerShippingAddressRequest $request): JsonResponse
    {
        $shippingAddressDto = CreateCustomerShippingAddressDto::fromRequest($request->validated());

        try {
            $data = $this->createCustomerShippingAddress->execute($shippingAddressDto);

            return ApiResponse::success('Shipping address successfully created', $data, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
