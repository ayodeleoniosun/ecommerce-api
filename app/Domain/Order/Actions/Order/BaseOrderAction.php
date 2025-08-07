<?php

namespace App\Domain\Order\Actions\Order;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use Illuminate\Database\Eloquent\Model;

class BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    protected function getValidOrderPayment(string $reference): Model
    {
        $orderPayment = $this->orderPaymentRepository->findByColumn(
            OrderPayment::class,
            'reference',
            $reference,
        );

        throw_if(! $orderPayment, ResourceNotFoundException::class,
            PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

        $orderAlreadyCompleted = in_array($orderPayment->status, self::completedTransactionStatuses()) ||
            in_array($orderPayment->order->status, self::completedTransactionStatuses());

        throw_if($orderAlreadyCompleted, ConflictHttpException::class,
            PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value);

        return $orderPayment;
    }

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
