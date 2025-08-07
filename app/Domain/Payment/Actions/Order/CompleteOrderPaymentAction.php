<?php

namespace App\Domain\Payment\Actions\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\BaseOrderAction;
use App\Domain\Order\Enums\CartStatusEnum;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\Cart\UserCartRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Notifications\OrderCompletedNotification;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompleteOrderPaymentAction extends BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserCartRepositoryInterface $userCartRepository,
        private readonly UserCartItemRepositoryInterface $userCartItemRepository,
    ) {
        parent::__construct(
            $orderItemRepository,
            $orderPaymentRepository,
        );
    }

    public function execute(PaymentResponseDto $transactionResponse): OrderResource
    {
        $orderPayment = $this->getValidOrderPayment($transactionResponse->getReference());

        $order = null;

        DB::transaction(function () use (&$order, $orderPayment, $transactionResponse) {
            $order = $orderPayment->order;

            $cart = $this->userCartRepository->findPendingCart($order->user_id, lockForUpdate: true);

            $amountCharged = $orderPayment->order_amount + $orderPayment->delivery_amount + $transactionResponse->getFee() + $transactionResponse->getVat();

            $this->orderPaymentRepository->updateColumns($orderPayment, [
                'status' => $transactionResponse->getStatus(),
                'fee' => $transactionResponse->getFee(),
                'vat' => $transactionResponse->getVat(),
                'amount_charged' => $amountCharged,
                'auth_model' => $transactionResponse->getAuthModel() ?? null,
                'narration' => $transactionResponse->getResponseMessage(),
                'completed_at' => now()->toDateTimeString(),
            ]);

            if ($transactionResponse->getStatus() === OrderStatusEnum::SUCCESS->value) {
                $order = $this->completeOrder($transactionResponse->getStatus(), $orderPayment, $cart);
            }
        });

        if ($transactionResponse->getStatus() === OrderStatusEnum::SUCCESS->value) {
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
