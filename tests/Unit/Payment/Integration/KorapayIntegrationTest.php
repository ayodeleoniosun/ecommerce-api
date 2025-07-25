<?php

namespace Tests\Unit\Payment\Integration;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
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

    $this->paymentDto = new InitiateCardPaymentDto(
        amount: 1000,
        currency: CurrencyEnum::NGN->value,
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

    $this->cardTransactionMock = Mockery::mock(TransactionKoraCardPayment::class)->makePartial();
    $this->cardTransactionMock->id = 1;

    $this->apiLogCardTransactionMock = Mockery::mock(ApiLogsKoraCardPayment::class)->makePartial();
    $this->apiLogCardTransactionMock->id = 1;

    $this->cardTransactionMock->apiLog = $this->apiLogCardTransactionMock;
});

it('should initialize charge successfully using PIN auth model', function () {
    $this->cardTransactionMock->shouldReceive('load')
        ->once()
        ->with('apiLog')
        ->andReturnSelf();

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            $this->paymentDto->toTransactionArray(),
        )->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            ['transaction_id' => $this->cardTransactionMock->id],
        )->andReturn($this->apiLogCardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->apiLogCardTransactionMock);

    $this->korapayIntegration->shouldReceive('initializeCharge')
        ->once()
        ->with($this->paymentDto)
        ->andReturn([
            'status' => true,
            'message' => 'Card charged successfully',
            'data' => [
                'amount' => 490000,
                'amount_charged' => 490000,
                'auth_model' => AuthModelEnum::PIN->value,
                'currency' => CurrencyEnum::NGN,
                'fee' => 0,
                'vat' => 0,
                'response_message' => 'Card charged successfully',
                'payment_reference' => 'KPY-12345-ref',
                'status' => 'success',
                'transaction_reference' => 'KPY-1234-ref',
            ],
        ]);

    $response = $this->korapayIntegration->initiate($this->paymentDto);

    expect($response)->toBeInstanceOf(PaymentResponseDto::class)
        ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
        ->and($response->getAuthModel())->toBe(AuthModelEnum::PIN->value)
        ->and($response->getAmountCharged())->toBe(490000)
        ->and($response->getFee())->toBe(0)
        ->and($response->getErrorType())->toBeNull()
        ->and($response->getRedirectionUrl())->toBeNull()
        ->and($response->getVat())->toBe(0);
});

it('should initialize charge successfully using OTP auth model', function () {
    $this->orderPayment->auth_model = AuthModelEnum::OTP->value;
    $this->orderPayment->save();

    $this->cardTransactionMock->shouldReceive('load')
        ->once()
        ->with('apiLog')
        ->andReturnSelf();

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            $this->paymentDto->toTransactionArray(),
        )->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            ['transaction_id' => $this->cardTransactionMock->id],
        )->andReturn($this->apiLogCardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->cardTransactionMock);

    $this->korapayIntegration->shouldReceive('initializeCharge')
        ->once()
        ->with($this->paymentDto)
        ->andReturn([
            'status' => true,
            'message' => 'Card charged successfully',
            'data' => [
                'amount' => 490000,
                'amount_charged' => 490000,
                'auth_model' => AuthModelEnum::OTP->value,
                'currency' => CurrencyEnum::NGN,
                'fee' => 0,
                'vat' => 0,
                'response_message' => 'Card charged successfully',
                'payment_reference' => 'KPY-12345-ref',
                'status' => 'success',
                'transaction_reference' => 'KPY-1234-ref',
            ],
        ]);

    $response = $this->korapayIntegration->initiate($this->paymentDto);

    expect($response)->toBeInstanceOf(PaymentResponseDto::class)
        ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
        ->and($response->getAuthModel())->toBe(AuthModelEnum::OTP->value)
        ->and($response->getErrorType())->toBeNull()
        ->and($response->getRedirectionUrl())->toBeNull();
});

it('should initialize charge successfully using AVS auth model', function () {
    $this->orderPayment->auth_model = AuthModelEnum::AVS->value;
    $this->orderPayment->save();

    $this->cardTransactionMock->shouldReceive('load')
        ->once()
        ->with('apiLog')
        ->andReturnSelf();

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            $this->paymentDto->toTransactionArray(),
        )->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('create')
        ->once()
        ->with(
            ApiLogsKoraCardPayment::class,
            ['transaction_id' => $this->cardTransactionMock->id],
        )->andReturn($this->apiLogCardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionKoraCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->cardTransactionMock);

    $this->korapayIntegration->shouldReceive('initializeCharge')
        ->once()
        ->with($this->paymentDto)
        ->andReturn([
            'status' => true,
            'message' => 'Card charged successfully',
            'data' => [
                'amount' => 490000,
                'amount_charged' => 490000,
                'auth_model' => AuthModelEnum::AVS->value,
                'currency' => CurrencyEnum::NGN,
                'fee' => 0,
                'vat' => 0,
                'response_message' => 'Card charged successfully',
                'payment_reference' => 'KPY-12345-ref',
                'status' => 'success',
                'transaction_reference' => 'KPY-1234-ref',
            ],
        ]);

    $response = $this->korapayIntegration->initiate($this->paymentDto);

    expect($response)->toBeInstanceOf(PaymentResponseDto::class)
        ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
        ->and($response->getAuthModel())->toBe(AuthModelEnum::AVS->value)
        ->and($response->getErrorType())->toBeNull()
        ->and($response->getRedirectionUrl())->toBeNull();
});
