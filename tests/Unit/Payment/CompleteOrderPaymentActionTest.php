<?php

namespace Tests\Unit\Payment;

use App\Domain\Order\Enums\CartStatusEnum;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Cart\UserCartItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Notifications\OrderCompletedNotification;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Actions\Order\CompleteOrderPaymentAction;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Cart\UserCartRepository;
use App\Infrastructure\Repositories\Order\OrderItemRepository;
use App\Infrastructure\Repositories\Order\OrderPaymentRepository;
use Illuminate\Support\Facades\Notification;
use Mockery;

beforeEach(function () {
    $this->orderRepo = Mockery::mock(OrderRepositoryInterface::class);
    $this->orderPaymentRepo = Mockery::mock(OrderPaymentRepository::class)->makePartial();
    $this->orderItemRepo = Mockery::mock(OrderItemRepository::class)->makePartial();
    $this->userCartRepo = Mockery::mock(UserCartRepository::class)->makePartial();
    $this->userCartItemRepo = Mockery::mock(UserCartItemRepositoryInterface::class);

    $this->user = User::factory()->create();

    $this->actingAs($this->user, 'sanctum');

    $this->cart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->order = Order::factory()->create([
        'cart_id' => $this->cart->id,
        'user_id' => $this->user->id,
    ]);

    $this->orderPayment = OrderPayment::factory()->create([
        'order_id' => $this->order->id,
        'order_amount' => 20000,
        'reference' => 'KPY-12345',
    ]);

    $this->paymentResponseDto = new PaymentResponseDto(
        status: PaymentStatusEnum::SUCCESS->value,
        paymentMethod: PaymentTypeEnum::CARD->value,
        reference: $this->orderPayment->reference,
        responseMessage: 'Payment successful',
        authModel: AuthModelEnum::PIN->value,
        gateway: 'korapay',
        amountCharged: 10000,
        fee: 100,
        vat: 10,
    );

    $this->completeOrderPayment = new CompleteOrderPaymentAction(
        $this->orderItemRepo,
        $this->orderPaymentRepo,
        $this->orderRepo,
        $this->userCartRepo,
        $this->userCartItemRepo
    );
});

describe('Complete Order Payment', function () {
    it('should complete order payment', function () {
        Notification::fake();

        $this->userCartRepo->shouldReceive('findPendingCart')
            ->once()
            ->with($this->user->id, true)
            ->andReturn($this->cart);

        $this->order->status = CartStatusEnum::CHECKED_OUT->value;

        $this->orderRepo->shouldReceive('storeOrUpdate')
            ->once()
            ->with([
                'id' => $this->order->id,
                'status' => $this->paymentResponseDto->getStatus(),
            ])->andReturn($this->order);

        $this->userCartItemRepo->shouldReceive('completeCartItems')
            ->once()
            ->with($this->cart->id, CartStatusEnum::CHECKED_OUT->value)
            ->andReturn(true);

        $response = $this->completeOrderPayment->execute($this->paymentResponseDto);

        $amountCharged = ($this->orderPayment->order_amount + $this->orderPayment->delivery_amount + $this->paymentResponseDto->getFee() + $this->paymentResponseDto->getVat());

        Notification::assertSentTo(
            $this->user,
            OrderCompletedNotification::class,
            function ($notification) use ($response) {
                return $notification->order->id === $response->resource->user_id;
            });

        expect($response)->toBeInstanceOf(OrderResource::class)
            ->and($response->resource->currency)->toBe($this->order->currency)
            ->and($response->resource->payment->amount_charged)->toBe((int) $amountCharged)
            ->and($response->resource->payment->status)->toBe(OrderStatusEnum::SUCCESS->value)
            ->and($response->resource->payment->narration)->toBe($this->paymentResponseDto->getResponseMessage());
    });
});
