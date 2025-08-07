<?php

namespace Tests\Unit\Payment\Integration;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Domain\Payment\Dtos\Card\CardData;
use App\Domain\Payment\Dtos\Card\CustomerData;
use App\Domain\Payment\Dtos\Card\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\ApiLogsFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Services\Payments\Flutterwave\FlutterwaveIntegration;
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
    ]);

    $this->paymentDto = new InitiateCardPaymentDto(
        amount: 1000,
        currency: CurrencyEnum::NGN->value,
        card: new CardData(
            name: fake()->firstName().' FlutterwaveIntegrationTest.php'.fake()->lastName(),
            number: fake()->creditCardNumber(),
            cvv: '123',
            expiryMonth: fake()->month(),
            expiryYear: fake()->year(),
            pin: '1234',
        ),
        customer: new CustomerData(
            email: fake()->email(),
            name: fake()->firstName().' FlutterwaveIntegrationTest.php'.fake()->lastName(),
        ),
        redirectUrl: 'https://example.com',
        orderPaymentReference: 'KPY-12345',
    );

    $this->cardTransactionMock = Mockery::mock(TransactionFlutterwaveCardPayment::class)->makePartial();
    $this->cardTransactionMock->id = 1;

    $this->apiLogCardTransactionMock = Mockery::mock(ApiLogsFlutterwaveCardPayment::class)->makePartial();
    $this->apiLogCardTransactionMock->id = 1;

    $this->cardTransactionMock->apiLog = $this->apiLogCardTransactionMock;
});

describe('Flutterwave Integration', function () {
    it('should return an error response if request is not successful', function () {
        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->with(
                TransactionFlutterwaveCardPayment::class,
                $this->paymentDto->toTransactionArray(),
            )->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->with(
                ApiLogsFlutterwaveCardPayment::class,
                ['transaction_id' => $this->cardTransactionMock->id],
            )->andReturn($this->apiLogCardTransactionMock);

        $this->flutterwaveIntegration->shouldReceive('initializeCharge')
            ->once()
            ->with($this->paymentDto)
            ->andReturn([
                'status' => 'failed',
                'message' => 'Request not succcesful',
            ]);

        $response = $this->flutterwaveIntegration->initiate($this->paymentDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getGateway())->toBe(GatewayEnum::FLUTTERWAVE->value)
            ->and($response->getAuthModel())->toBeNull()
            ->and($response->getAmountCharged())->toBeNull()
            ->and($response->getFee())->toBe(0.0)
            ->and($response->getVat())->toBe(0.0)
            ->and($response->getRedirectionUrl())->toBeNull();
    });

    it('should initialize charge successfully using OTP auth model', function () {
        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->with(
                TransactionFlutterwaveCardPayment::class,
                $this->paymentDto->toTransactionArray(),
            )->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->with(
                ApiLogsFlutterwaveCardPayment::class,
                ['transaction_id' => $this->cardTransactionMock->id],
            )->andReturn($this->apiLogCardTransactionMock);

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

        $this->flutterwaveIntegration->shouldReceive('initializeCharge')
            ->once()
            ->with($this->paymentDto)
            ->andReturn([
                'status' => 'success',
                'message' => 'Charge initiated',
                'data' => [
                    'id' => 12345,
                    'tx_ref' => 'PAY-123456',
                    'flw_ref' => 'FLW-MOCK-abcdefgh',
                    'device_fingerprint' => 'N/A',
                    'amount' => 1000,
                    'charged_amount' => 1000,
                    'app_fee' => 14,
                    'merchant_fee' => 0,
                    'processor_response' => 'Please enter the OTP sent to your mobile number 080****** and email te**@rave**.com',
                    'auth_model' => 'VBVSECURECODE',
                    'currency' => 'NGN',
                    'ip' => '11.12.13.14',
                    'narration' => 'CARD Transaction ',
                    'status' => 'pending',
                    'payment_type' => 'card',
                    'plan' => null,
                    'fraud_status' => 'ok',
                    'charge_type' => 'normal',
                    'created_at' => '2025-07-31T12:57:32.000Z',
                    'account_id' => 23156,
                    'customer' => [
                        'id' => 3338930,
                        'phone_number' => null,
                        'name' => 'Ayodele',
                        'email' => 'ayodeleoniosun63@gmail.com',
                        'created_at' => '2025-07-31T12:57:32.000Z',
                    ],
                    'card' => [
                        'first_6digits' => '123456',
                        'last_4digits' => '4567',
                        'issuer' => 'MASTERCARD  CREDIT',
                        'country' => 'NG',
                        'type' => 'MASTERCARD',
                        'expiry' => '09/32',
                    ],
                ],
                'meta' => [
                    'authorization' => [
                        'mode' => 'redirect',
                        'redirect' => 'https://ravesandboxapi.flutterwave.com/mockvbvpage?ref=FLW-MOCK-abcdedfgh&code=00&message=Approved.Successful&receiptno=RN12345678',
                    ],
                ],
            ]);

        $response = $this->flutterwaveIntegration->initiate($this->paymentDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::PROCESSING->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::OTP->value)
            ->and($response->getGateway())->toBe(GatewayEnum::FLUTTERWAVE->value)
            ->and($response->getAmountCharged())->toBe(1000.0)
            ->and($response->getFee())->toBe(14.0)
            ->and($response->getVat())->toBe(0.0)
            ->and($response->getRedirectionUrl())->toBeUrl();
    });

    it('should verify transaction if found', function () {
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

        $this->cardTransactionMock->gateway_transaction_reference = $this->paymentDto->getOrderPaymentReference();

        $this->cardTransactionRepo->shouldReceive('findByColumn')
            ->andReturn($this->cardTransactionMock);

        $this->flutterwaveIntegration->shouldReceive('verifyCharge')
            ->once()
            ->with($this->paymentDto->getOrderPaymentReference())
            ->andReturn([
                'status' => 'success',
                'message' => 'Transaction fetched successfully',
                'data' => [
                    'id' => 9526134,
                    'tx_ref' => 'AY-12345612',
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
            ]);

        $response = $this->flutterwaveIntegration->verify($this->paymentDto->getOrderPaymentReference());

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::OTP->value)
            ->and($response->getAmountCharged())->toBe(1000.0)
            ->and($response->getFee())->toBe(14.0);
    });
});
