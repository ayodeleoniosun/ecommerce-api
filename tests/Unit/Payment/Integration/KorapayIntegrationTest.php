<?php

namespace Tests\Unit\Payment\Integration;

use App\Domain\Payment\Constants\Currencies;
use App\Domain\Payment\Constants\PaymentStatusEnum;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Services\Payments\Korapay\KorapayIntegration;
use Mockery;

beforeEach(function () {
    $this->cardTransactionRepo = Mockery::mock(CardTransactionRepositoryInterface::class);
    $this->korapayIntegration = Mockery::mock(KorapayIntegration::class, [$this->cardTransactionRepo])->makePartial();

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
    ]);

    $this->cardTransaction = TransactionKoraCardPayment::factory()->create([
        'order_payment_id' => $this->orderPayment->id,
    ]);
});

it('should initialize charge successfully and mock endpoint response', function () {
    $paymentDto = new InitiateOrderPaymentDto(
        amount: 1000,
        currency: Currencies::NGN->value,
        card: new CardData(
            name: fake()->firstName().' '.fake()->lastName(),
            number: fake()->creditCardNumber(),
            cvv: '123',
            expiryMonth: fake()->month(),
            expiryYear: fake()->year(),
            pin: '1234',
        ),
        customer: new CustomerData(
            email: fake()->email(),
            name: fake()->firstName().' '.fake()->lastName(),
        ),
        redirectUrl: 'https://example.com',
        reference: 'KPY-12345',
        paymentId: $this->orderPayment->id,
    );

    $cardTransactionMock = Mockery::mock(TransactionKoraCardPayment::class)->makePartial();
    $cardTransactionMock->id = 1;

    $apiLogCardTransactionMock = Mockery::mock(ApiLogsKoraCardPayment::class)->makePartial();
    $apiLogCardTransactionMock->id = 1;

    $cardTransactionMock->apiLog = $apiLogCardTransactionMock;

    $cardTransactionMock->shouldReceive('load')
        ->once()
        ->with('apiLog')
        ->andReturnSelf();

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            $paymentDto->toTransactionArray(),
        )->andReturn($cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            ['transaction_id' => $cardTransactionMock->id],
        )->andReturn($apiLogCardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($apiLogCardTransactionMock);

    $this->korapayIntegration->shouldReceive('initializeCharge')
        ->once()
        ->with($paymentDto)
        ->andReturn([
            'status' => true,
            'message' => 'Card charged successfully',
            'data' => [
                'amount' => 490000,
                'amount_charged' => 490000,
                'auth_model' => 'PIN',
                'currency' => 'NGN',
                'fee' => 0,
                'vat' => 0,
                'response_message' => 'Card charged successfully',
                'payment_reference' => 'KPY-12345-ref',
                'status' => 'success',
                'transaction_reference' => 'KPY-1234-ref',
            ],
        ]);

    $response = $this->korapayIntegration->initiate($paymentDto);

    expect($response)->toBeInstanceOf(PaymentResponseDto::class)
        ->and($response->getAmountCharged())->toBe(490000)
        ->and($response->getFee())->toBe(0)
        ->and($response->getVat())->toBe(0)
        ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value);
});
