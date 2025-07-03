<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Infrastructure\Models\Order\Order;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    public function execute(int $orderId, array $transactionResponse): OrderResource
    {
        $order = $this->orderRepository->findByColumn(
            Order::class,
            'id',
            $orderId,
        );

        throw_if(! $order, ResourceNotFoundException::class, 'Order request invalid');

        DB::transaction(function () use (&$order, $transactionResponse) {
            $orderPayment = $order->payments->last();

            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse['fee'] + $transactionResponse['vat'];

            $this->orderPaymentRepository->storeOrUpdate([
                'order_id' => $order->id,
                'status' => $transactionResponse['status'],
                'fee' => $transactionResponse['fee'],
                'vat' => $transactionResponse['vat'],
                'amount_charged' => $amountCharged,
                'gateway' => $transactionResponse['gateway'],
                'gateway_reference' => $transactionResponse['gateway_reference'],
                'narration' => $transactionResponse['gateway_response_message'],
                'completed_at' => now()->toDateTimeString(),
            ]);

            $order = $this->orderRepository->storeOrUpdate([
                'id' => $order->id,
                'status' => $transactionResponse['status'],
            ]);
        });

        $record = $order->load('items', 'shipping', 'payments');

        return new OrderResource($record);
    }
}
