<?php

namespace Tests\Unit\Payment\Integration\Flutterwave;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Payment\Dtos\Webhook\FlutterwaveWebhookDto;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Events\PaymentWebhookCompleted;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\ApiLogsFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Services\Payments\Flutterwave\FlutterwaveIntegration;
use App\Infrastructure\Services\Payments\Flutterwave\Webhook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Mockery;

beforeEach(function () {
    $this->cardTransactionRepo = Mockery::mock(CardTransactionRepositoryInterface::class);
    $this->flutterwaveIntegration = Mockery::mock(FlutterwaveIntegration::class,
        [$this->cardTransactionRepo])->makePartial();

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

    $this->cardTransaction = TransactionFlutterwaveCardPayment::factory()->create([
        'order_payment_reference' => $this->orderPayment->reference,
        'reference' => 'FLW-Z25EFS7YZZXC8WVT',
    ]);

    $this->flutterwaveWebhookDto = new FlutterwaveWebhookDto(
        id: 1234,
        transactionReference: $this->orderPayment->reference,
        amount: $this->cardTransaction->amount,
        chargedAmount: $this->cardTransaction->amount,
        status: PaymentStatusEnum::SUCCESSFUL->value
    );

    $this->cardTransactionMock = Mockery::mock(TransactionFlutterwaveCardPayment::class)->makePartial();
    $this->cardTransactionMock->id = 1;
    $this->cardTransactionMock->status = PaymentStatusEnum::SUCCESSFUL->value;
    $this->cardTransactionMock->gateway_transaction_reference = '123456';
    $this->cardTransactionMock->gateway_response = 'Message goes here';

    $this->apiLogCardTransactionMock = Mockery::mock(ApiLogsFlutterwaveCardPayment::class)->makePartial();
    $this->apiLogCardTransactionMock->id = 1;

    $this->cardTransactionMock->apiLog = $this->apiLogCardTransactionMock;

    $this->webhook = new Webhook($this->cardTransactionRepo);
});

it('should return an exception if reference is invalid', function () {
    $this->cardTransactionRepo->shouldReceive('findByColumn')
        ->andReturn(null);

    $this->webhook->execute($this->flutterwaveWebhookDto);
})->throws(ResourceNotFoundException::class, 'Transaction does not exist');

it('should complete webhook if status is success', function () {
    Event::fake();

    $this->cardTransactionRepo->shouldReceive('findByColumn')
        ->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionFlutterwaveCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            ApiLogsFlutterwaveCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->apiLogCardTransactionMock);

    Http::fake([
        '*' => Http::response([
            'status' => 'success',
            'message' => 'Transaction fetched successfully',
            'data' => [
                'id' => 9526134,
                'tx_ref' => $this->flutterwaveWebhookDto->getTransactionReference(),
                'flw_ref' => 'FLW-MOCK-abcdef123',
                'device_fingerprint' => 'N/A',
                'amount' => 1000,
                'currency' => 'NGN',
                'charged_amount' => 1000,
                'app_fee' => 14,
                'merchant_fee' => 0,
                'processor_response' => 'Approved. Successful',
                'auth_model' => 'VBVSECURECODE',
                'ip' => '11.12.13.14',
                'narration' => 'CARD Transaction',
                'status' => 'successful',
                'payment_type' => 'card',
                'created_at' => '2025-07-31T12:57:32.000Z',
                'account_id' => 12345,
                'card' => [
                    'first_6digits' => '12345',
                    'last_4digits' => '1234',
                    'issuer' => ' CREDIT',
                    'country' => 'NIGERIA NG',
                    'type' => 'MASTERCARD',
                    'expiry' => '09/32',
                ],
                'meta' => null,
                'amount_settled' => 984.95,
                'customer' => [
                    'id' => 123456,
                    'email' => 'ayodeleoniosun63@gmail.com',
                    'created_at' => '2025-07-31T12:57:32.000Z',
                ],
            ],
        ]),
    ]);

    $this->webhook->execute($this->flutterwaveWebhookDto);

    Event::assertDispatched(function (PaymentWebhookCompleted $event) {
        return $event->paymentResponseDto->getStatus() === PaymentStatusEnum::SUCCESS->value &&
            $event->paymentResponseDto->getReference() === $this->flutterwaveWebhookDto->getTransactionReference();
    });
});

it('should complete webhook if status is failed', function () {
    Event::fake();

    $this->cardTransactionMock->status = PaymentStatusEnum::FAILED->value;
    $this->flutterwaveWebhookDto->setStatus(PaymentStatusEnum::FAILED->value);

    $this->cardTransactionRepo->shouldReceive('findByColumn')
        ->andReturn($this->cardTransactionMock);

    $this->cardTransactionRepo->shouldReceive('update')
        ->once()
        ->with(
            TransactionFlutterwaveCardPayment::class,
            Mockery::type('array'),
        )->andReturn($this->cardTransactionMock);

    $this->webhook->execute($this->flutterwaveWebhookDto);

    Event::assertDispatched(function (PaymentWebhookCompleted $event) {
        return $event->paymentResponseDto->getStatus() === PaymentStatusEnum::FAILED->value &&
            $event->paymentResponseDto->getReference() === $this->flutterwaveWebhookDto->getTransactionReference();
    });
});
