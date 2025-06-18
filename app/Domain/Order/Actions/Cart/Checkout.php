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
use App\Infrastructure\Models\Order\Order;
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

    public function execute(CheckoutDto $checkoutDto): Order
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

        $order = null;

        DB::transaction(function () use (&$order, $checkoutDto, $customerAddress, $pickupStation) {
            $order = $this->orderRepository->findOrCreate();
            $this->createOrderItems($order->id);
            $this->createOrderShipping($order->id, $checkoutDto->getDeliveryType(), $customerAddress, $pickupStation);
            $this->createOrderPayment($order->id);
        });

        return $order->load('items', 'shipping', 'payments');
    }

    public function createOrderItems(int $orderId): void
    {
        $cart = $this->userCartRepository->findPendingCart(auth()->user()->id);
        $cartItems = $cart->items->toArray();

        $currentOrderItems = $this->orderItemRepository->findAllByColumn(
            OrderItem::class,
            'order_id',
            $orderId,
        )->toArray();

        $difference = $this->differentiateCartItems($currentOrderItems, $cartItems);

        if (empty($difference)) {
            $cart->items->each(fn ($item) => $this->storeItem($orderId, $item));

            return;
        }

        if ($difference['canDelete']) {
            $productItemIds = array_column($difference['items'], 'product_item_id');
            $this->orderItemRepository->deleteOrderItems($orderId, $productItemIds);

            return;
        }

        foreach ($difference['items'] as $item) {
            $this->storeItem($orderId, (object) $item);
        }
    }

    public function differentiateCartItems(array $currentOrderItems, array $newCartItems): array
    {
        $orderItemsId = array_column($currentOrderItems, 'product_item_id');
        $cartItemsId = array_column($newCartItems, 'product_item_id');

        $canDelete = count($cartItemsId) < count($orderItemsId);
        $compareSource = $canDelete ? $currentOrderItems : $newCartItems;
        $diffIds = $canDelete
            ? array_diff($orderItemsId, $cartItemsId)
            : array_diff($cartItemsId, $orderItemsId);

        $items = array_values(array_filter($compareSource, function ($item) use ($diffIds) {
            return in_array($item['product_item_id'], $diffIds);
        }));

        return compact('canDelete', 'items');
    }

    private function storeItem(int $orderId, object $item): void
    {
        $this->orderItemRepository->storeOrUpdate([
            'order_id' => $orderId,
            'product_item_id' => $item->product_item_id,
            'quantity' => $item->quantity,
            'total_amount' => $item->quantity * $item->product_item['price'],
        ]);
    }

    public function createOrderShipping(
        int $orderId,
        string $deliveryType,
        Model $customerAddress,
        ?Model $pickupStation,
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
