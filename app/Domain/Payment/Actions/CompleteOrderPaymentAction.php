<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    public function execute(array $transactionResponse): OrderResource
    {
        $order = $this->orderRepository->findPendingOrder(auth()->user()->id);

        throw_if(! $order, ResourceNotFoundException::class, 'No order is in progress');

        DB::transaction(function () use (&$order, $transactionResponse) {
            $orderPayment = $order->payment;

            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse['fee'] + $transactionResponse['vat'];

            $this->orderPaymentRepository->updateByColumns(
                $orderPayment,
                [
                    'order_id' => $order->id,
                    'status' => $transactionResponse['status'],
                    'fee' => $transactionResponse['fee'],
                    'vat' => $transactionResponse['vat'],
                    'amount_charged' => $amountCharged,
                    'gateway' => $transactionResponse['gateway'],
                    'gateway_reference' => $transactionResponse['gateway_reference'],
                    'narration' => $transactionResponse['gateway_response_message'],
                    'completed_at' => now()->toDateTimeString(),
                ],
            );

            if ($transactionResponse['status'] === OrderStatusEnum::SUCCESS->value) {
                $order = $this->orderRepository->storeOrUpdate([
                    'id' => $order->id,
                    'status' => $transactionResponse['status'],
                ]);
            }
        });

        $record = $order->load('items', 'shipping', 'payment');

        return new OrderResource($record);
    }
}
