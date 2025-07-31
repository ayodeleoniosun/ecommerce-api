<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Enums\CartStatusEnum;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\Cart\UserCartRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Notifications\OrderCompletedNotification;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use Illuminate\Database\Eloquent\Model;
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
        $orderPayment = $this->orderPaymentRepository->findByColumn(
            OrderPayment::class,
            'reference',
            $transactionResponse->getReference(),
        );

        throw_if(! $orderPayment, ResourceNotFoundException::class,
            PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

        $order = null;
        $status = $transactionResponse->getStatus();

        DB::transaction(function () use (&$order, $orderPayment, $transactionResponse, $status) {
            $order = $orderPayment->order;

            $cart = $this->userCartRepository->findPendingCart($order->user_id, lockForUpdate: true);

            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse->getFee() + $transactionResponse->getVat();

            $this->orderPaymentRepository->updateColumns($orderPayment, [
                'status' => $status,
                'fee' => $transactionResponse->getFee(),
                'vat' => $transactionResponse->getVat(),
                'amount_charged' => $amountCharged,
                'auth_model' => $transactionResponse->getAuthModel() ?? null,
                'narration' => $transactionResponse->getResponseMessage(),
                'completed_at' => now()->toDateTimeString(),
            ]);

            if ($status === OrderStatusEnum::SUCCESS->value) {
                $order = $this->completeOrder($status, $orderPayment, $cart);
            }
        });

        if ($status === OrderStatusEnum::SUCCESS->value) {
            $order->user->notify(new OrderCompletedNotification($order));
        }

        $record = $order->load('items', 'shipping', 'payment');

        return new OrderResource($record);
    }

    private function completeOrder(
        string $status,
        Model $orderPayment,
        UserCart $cart,
    ): Order {
        $order = $this->orderRepository->storeOrUpdate([
            'id' => $orderPayment->order->id,
            'status' => $status,
        ]);

        $cartStatus = CartStatusEnum::CHECKED_OUT->value;

        $this->userCartRepository->updateColumns($cart, [
            'status' => $cartStatus,
        ]);

        $this->userCartItemRepository->completeCartItems($cart->id, $cartStatus);

        return $order;
    }

    public function updateOrderPayment(PaymentResponseDto $transactionResponse): void
    {
        DB::transaction(function () use ($transactionResponse) {
            $order = $this->orderRepository->findPendingOrder(auth()->user()->id, lockForUpdate: true);

            if (! $order) {
                return;
            }

            $orderPayment = $order->payment;

            $this->orderPaymentRepository->updateColumns($orderPayment, [
                'auth_model' => $transactionResponse->getAuthModel() ?? null,
                'status' => $transactionResponse->getStatus(),
                'narration' => $transactionResponse->getResponseMessage(),
            ]);
        });
    }
}
