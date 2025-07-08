<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Application\Shared\Enum\OrderStatusEnum;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\UserCartRepositoryInterface;
use App\Domain\Order\Notifications\OrderCompletedNotification;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {}

    public function execute(PaymentResponseDto $transactionResponse): OrderResource
    {
        $order = null;

        DB::transaction(function () use (&$order, $transactionResponse) {
            $order = $this->orderRepository->findPendingOrder(auth()->user()->id, lockForUpdate: true);

            if (! $order) {
                return;
            }

            $cart = $this->userCartRepository->findPendingCart(auth()->user()->id, lockForUpdate: true);

            $orderPayment = $order->payment;
            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse->getFee() + $transactionResponse->getVat();
            $status = $transactionResponse->getStatus();

            $this->orderPaymentRepository->updateColumns(
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

            $cartStatus = $status === OrderStatusEnum::SUCCESS->value ? CartStatusEnum::CHECKED_OUT->value : $cart->status;

            $this->userCartRepository->updateColumns(
                $cart,
                ['status' => $cartStatus],
            );

            $this->userCartItemRepository->completeCartItems($cart->id, $cartStatus);
        });

        $order->user->notify(new OrderCompletedNotification($order));

        $record = $order->load('items', 'shipping', 'payment');

        return new OrderResource($record);
    }
}
