<?php

namespace App\Domain\Order\Actions\Cart;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Dtos\CheckoutDto;
use App\Domain\Order\Interfaces\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\OrderShippingRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Actions\InitiateOrderPaymentAction;
use App\Domain\Shipping\Interfaces\PickupStation\PickupStationRepositoryInterface;
use App\Domain\Shipping\Interfaces\ShippingAddress\CustomerShippingAddressRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checkout
{
    use UtilitiesTrait;

    public function __construct(
        private readonly PickupStationRepositoryInterface $pickupStationRepository,
        private readonly CustomerShippingAddressRepositoryInterface $customerShippingAddressRepository,
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderShippingRepositoryInterface $orderShippingRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
        private readonly InitiateOrderPaymentAction $initiatePaymentAction,
    ) {}

    public function execute(CheckoutDto $checkoutDto): OrderResource
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
            $order = $this->createOrder(auth()->user()->id);
            $this->createOrderItems($order);
            $this->createOrderShipping($order->id, $checkoutDto->getDeliveryType(), $customerAddress, $pickupStation);
            $this->createOrderPayment($order);
        });

        $orderPayment = $order->payments->last();

        $transactionResponse = $this->initiatePaymentAction->execute($orderPayment);

        $this->completeOrderPayment($orderPayment, $transactionResponse);

        $order->refresh();

        $record = $order->load('items', 'shipping', 'payments');

        return new OrderResource($record);
    }

    private function createOrder(int $userId): Order
    {
        $cart = $this->userCartRepository->findPendingCart($userId);

        throw_if(! $cart, ResourceNotFoundException::class, 'No existing cart for user');

        return $this->orderRepository->findOrCreate($userId, $cart);
    }

    private function createOrderItems(Order $order): void
    {
        $cartItems = $order->cart->items;

        $currentOrderItems = $this->orderItemRepository->findAllByColumn(
            OrderItem::class,
            'order_id',
            $order->id,
        )->toArray();

        $difference = $this->differentiateCartItems($currentOrderItems, $cartItems->toArray());

        if (empty($difference)) {
            $cartItems->each(fn ($item) => $this->storeItem($order->id, $item));

            return;
        }

        if ($difference['canDelete']) {
            $productItemIds = array_column($difference['items'], 'product_item_id');
            $this->orderItemRepository->deleteOrderItems($order->id, $productItemIds);

            return;
        }

        foreach ($difference['items'] as $item) {
            $this->storeItem($order->id, (object) $item);
        }
    }

    private function differentiateCartItems(array $currentOrderItems, array $newCartItems): array
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
            'cart_item_id' => $item->id,
            'order_id' => $orderId,
            'total_amount' => $item->quantity * $item->product_item['price'],
        ]);
    }

    private function createOrderShipping(
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

    private function createOrderPayment(Order $order): void
    {
        $totalOrderAmount = $this->calculateTotalOrderAmount($order->id);

        $this->orderPaymentRepository->storeOrUpdate([
            'order_id' => $order->id,
            'order_amount' => $totalOrderAmount,
            'currency' => $order->currency,
            'delivery_amount' => 1000,
        ]);
    }

    private function calculateTotalOrderAmount(int $orderId): int
    {
        $orderItems = $this->orderItemRepository->findAllByColumn(
            OrderItem::class,
            'order_id',
            $orderId,
        );

        return $orderItems->pluck('total_amount')->sum();
    }

    private function completeOrderPayment(OrderPayment $orderPayment, array $transactionResponse): void
    {
        DB::transaction(function () use ($orderPayment, $transactionResponse) {
            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse['fee'] + $transactionResponse['vat'];

            $this->orderPaymentRepository->storeOrUpdate([
                'order_id' => $orderPayment->order_id,
                'status' => $transactionResponse['status'],
                'fee' => $transactionResponse['fee'],
                'vat' => $transactionResponse['vat'],
                'amount_charged' => $amountCharged,
                'gateway' => $transactionResponse['gateway'],
                'gateway_reference' => $transactionResponse['gateway_reference'],
                'narration' => $transactionResponse['gateway_response_message'],
                'completed_at' => now()->toDateTimeString(),
            ]);

            $this->orderRepository->storeOrUpdate([
                'id' => $orderPayment->order->id,
                'status' => $transactionResponse['status'],
            ]);
        });
    }
}
