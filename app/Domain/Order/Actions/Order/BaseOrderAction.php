<?php

namespace App\Domain\Order\Actions\Order;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Order\Enums\OrderStatusEnum;
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

    protected function createOrderPayment(Order|Model $order): OrderPayment
    {
        $totalOrderAmount = $this->calculateTotalOrderAmount($order->id);

        throw_if($totalOrderAmount <= 0, BadRequestException::class, 'Total order amount must be greater than zero.');

        return $this->orderPaymentRepository->store([
            'status' => OrderStatusEnum::PENDING->value,
            'order_id' => $order->id,
            'order_amount' => $totalOrderAmount,
            'currency' => $order->currency,
            'delivery_amount' => 1000,
        ]);
    }

    protected function calculateTotalOrderAmount(int $orderId): int
    {
        $orderItems = $this->orderItemRepository->findAllByColumn(
            OrderItem::class,
            'order_id',
            $orderId,
        )->get();

        return $orderItems->pluck('total_amount')->sum();
    }
}
