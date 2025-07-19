<?php

namespace App\Domain\Order\Actions\Order;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use Illuminate\Database\Eloquent\Model;

class BaseOrderAction
{
    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    public function createOrderPayment(Order|Model $order): OrderPayment
    {
        $totalOrderAmount = $this->calculateTotalOrderAmount($order->id);

        return $this->orderPaymentRepository->storeOrUpdate([
            'status' => OrderStatusEnum::PENDING->value,
            'order_id' => $order->id,
            'order_amount' => $totalOrderAmount,
            'currency' => $order->currency,
            'delivery_amount' => 1000,
        ]);
    }

    public function calculateTotalOrderAmount(int $orderId): int
    {
        $orderItems = $this->orderItemRepository->findAllByColumn(
            OrderItem::class,
            'order_id',
            $orderId,
        )->get();

        return $orderItems->pluck('total_amount')->sum();
    }
}
