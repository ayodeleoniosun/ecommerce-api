<?php

namespace Tests\Unit\Payment;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Payment\Actions\Card\PayWithCardAction;
use App\Domain\Payment\Actions\Order\InitiateOrderPaymentAction;
use App\Domain\Payment\Actions\Wallet\PayWithWalletAction;
use App\Domain\Payment\Dtos\Card\PaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\User\User;
use Mockery;

beforeEach(function () {
    $this->orderRepository = Mockery::mock(OrderRepositoryInterface::class);
    $this->payWithCard = Mockery::mock(PayWithCardAction::class);
    $this->payWithWallet = Mockery::mock(PayWithWalletAction::class);

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

    $this->paymentDto = new PaymentDto(
        paymentMethod: PaymentTypeEnum::WALLET->value,
        card: [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'number' => fake()->creditCardNumber(),
            'cvv' => '123',
            'expiryMonth' => fake()->month(),
            'expiryYear' => '30',
            'pin' => '1234',
        ],
    );

    $this->initiateOrderPayment = new InitiateOrderPaymentAction(
        $this->orderRepository,
        $this->payWithCard,
        $this->payWithWallet
    );
});

describe('Initiate Order Payment', function () {
    it('should throw an exception if there is no pending order', function () {
        $this->orderRepository->shouldReceive('findPendingOrder')
            ->once()
            ->andReturnNull();

        $this->initiateOrderPayment->execute($this->paymentDto);
    })->throws(ResourceNotFoundException::class, 'You are yet to checkout');

    it('should successfully initiate order payment if payment type is wallet', function () {
        $this->orderRepository->shouldReceive('findPendingOrder')
            ->once()
            ->with($this->user->id)
            ->andReturn($this->order);

        $paymentResponseDto = new PaymentResponseDto(
            status: OrderStatusEnum::SUCCESS->value,
            paymentMethod: PaymentTypeEnum::WALLET->value,
            reference: 'WAL-12345',
            responseMessage: PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value,
        );

        $this->payWithWallet
            ->shouldReceive('execute')
            ->with($this->order)
            ->andReturn($paymentResponseDto);

        $response = $this->initiateOrderPayment->execute($this->paymentDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::WALLET->value)
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value)
            ->and($response->getStatus())->toBe(OrderStatusEnum::SUCCESS->value);
    });

    it('should successfully initiate order payment if payment type is card', function () {
        $this->paymentDto->setPaymentMethod(PaymentTypeEnum::CARD->value);

        $this->orderRepository->shouldReceive('findPendingOrder')
            ->once()
            ->with($this->user->id)
            ->andReturn($this->order);

        $paymentResponseDto = new PaymentResponseDto(
            status: OrderStatusEnum::SUCCESS->value,
            paymentMethod: PaymentTypeEnum::CARD->value,
            reference: 'KPY-12345',
            responseMessage: PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value,
            authModel: AuthModelEnum::PIN->value,
        );

        $this->payWithCard
            ->shouldReceive('execute')
            ->with($this->order, $this->paymentDto->getCardData())
            ->andReturn($paymentResponseDto);

        $response = $this->initiateOrderPayment->execute($this->paymentDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::PIN->value)
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value)
            ->and($response->getStatus())->toBe(OrderStatusEnum::SUCCESS->value);
    });
});
