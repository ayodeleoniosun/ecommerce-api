<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    public function execute(PaymentResponseDto $transactionResponse): OrderResource
    {
        $order = $this->orderRepository->findPendingOrder(auth()->user()->id);

        throw_if(! $order, ResourceNotFoundException::class, 'No order is in progress');

        DB::transaction(function () use (&$order, $transactionResponse) {
            $orderPayment = $order->payment;
            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse->getFee() + $transactionResponse->getVat();
            $status = $transactionResponse->getStatus();

            $this->orderPaymentRepository->updateByColumns(
                $orderPayment,
                [
                    'order_id' => $order->id,
                    'status' => $status,
                    'fee' => $transactionResponse->getFee(),
                    'vat' => $transactionResponse->getVat(),
                    'amount_charged' => $amountCharged,
                    'gateway' => $transactionResponse->getGateway(),
                    'gateway_reference' => $transactionResponse->getReference(),
                    'narration' => $transactionResponse->getResponseMessage(),
                    'completed_at' => now()->toDateTimeString(),
                ],
            );

            if ($status === OrderStatusEnum::SUCCESS->value) {
                $order = $this->orderRepository->storeOrUpdate([
                    'id' => $order->id,
                    'status' => $status,
                ]);
            }
        });

        $record = $order->load('items', 'shipping', 'payment');

        return new OrderResource($record);
    }
}
