<?php

namespace App\Domain\Order\Dtos;

use App\Application\Shared\Traits\UtilitiesTrait;

class CheckoutDto
{
    use UtilitiesTrait;

    public function __construct(
        private readonly string $customerAddressUUID,
        private readonly string $pickupStationUUID,
        private readonly string $deliveryType,
        private readonly string $paymentMethod,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            customerAddressUUID: $payload['customer_address_id'],
            pickupStationUUID: $payload['pickup_station_id'],
            deliveryType: $payload['delivery_type'],
            paymentMethod: $payload['payment_method']
        );
    }

    public function toArray(): array
    {
        return [
            'customer_address_uuid' => $this->customerAddressUUID,
            'pickup_station_uuid' => $this->pickupStationUUID,
            'delivery_type' => $this->deliveryType,
            'payment_method' => $this->paymentMethod,
        ];
    }

    public function getCustomerAddressUUID(): string
    {
        return $this->customerAddressUUID;
    }

    public function getPickupStationUUID(): string
    {
        return $this->pickupStationUUID;
    }

    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
}
