<?php

namespace App\Domain\Order\Actions\Cart;

use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Interfaces\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\OrderShippingRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checkout
{
    public function __construct(
        private readonly PickupStationRepositoryInterface $pickupStationRepository,
        private readonly CustomerShippingAddressRepositoryInterface $customerShippingAddressRepository,
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderShippingRepositoryInterface $orderShippingRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    public function execute(CheckoutDto $checkoutDto): void
    {
        $pickupStation = null;

        if ($checkoutDto->getPickupStationUUID()) {
            $pickupStation = $this->pickupStationRepository->findByColumn(
                PickupStation::class,
                'uuid',
                $checkoutDto->getPickupStationUUID(),
            );
        }

        $customerAddress = $this->customerShippingAddressRepository->findByColumn(
            CustomerShippingAddress::class,
            'uuid',
            $checkoutDto->getCustomerAddressUUID(),
        );

        DB::transaction(function () use ($checkoutDto, $customerAddress, $pickupStation) {
            $order = $this->orderRepository->findOrCreate();
            $this->createOrderItems($order->id);
            $this->createOrderShipping($order->id, $checkoutDto->getDeliveryType(), $customerAddress, $pickupStation);
            $this->createOrderPayment($order->id);
        });
    }

    public function createOrderItems(int $orderId): void
    {
        $cart = $this->userCartRepository->findPendingCart(auth()->user()->id);

        $this->orderItemRepository->deleteByColumn(
            OrderItem::class,
            'order_id',
            $orderId,
        );

        $cart->items->each(function ($item) use ($orderId) {
            $this->orderItemRepository->storeOrUpdate([
                'order_id' => $orderId,
                'product_item_id' => $item->product_item_id,
                'quantity' => $item->quantity,
                'total_amount' => $item->quantity * $item->productItem->price,
            ]);
        });
    }

    public function createOrderShipping(
        int $orderId,
        string $deliveryType,
        Model $customerAddress,
        Model $pickupStation,
    ): void {
        $this->orderShippingRepository->storeOrUpdate([
            'order_id' => $orderId,
            'country_id' => $customerAddress->country_id,
            'state_id' => $customerAddress->state_id,
            'city_id' => $customerAddress->city_id,
            'delivery_type' => $deliveryType,
            'delivery_address' => $customerAddress->address,
            'pickup_station_name' => $pickupStation?->name,
            'pickup_station_address' => $pickupStation?->address,
            'pickup_station_contact_name' => $pickupStation?->contact_name,
            'pickup_station_contact_phone_number' => $pickupStation?->contact_phone_number,
            'estimated_delivery_start_date' => now()->addDays(5)->toDateString(),
            'estimated_delivery_end_date' => now()->addDays(7)->toDateString(),
        ]);
    }

    public function createOrderPayment($orderId): void
    {
        $this->orderPaymentRepository->storeOrUpdate([
            'order_id' => $orderId,
            'order_amount' => 1000,
            'delivery_amount' => 1000,
            'total_amount' => 1000,
            'amount_paid' => 1000,
        ]);
    }
}
