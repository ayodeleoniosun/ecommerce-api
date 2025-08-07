<?php

namespace Tests\Unit\Payment;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Payment\Actions\Order\AuthorizeOrderPaymentAction;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Order\OrderPaymentRepository;
use App\Infrastructure\Repositories\Payment\CardTransactionRepository;
use Illuminate\Support\Facades\Http;
use Mockery;

beforeEach(function () {
    $this->orderItemRepo = Mockery::mock(OrderItemRepositoryInterface::class);
    $this->orderPaymentRepo = Mockery::mock(OrderPaymentRepository::class)->makePartial();
    $this->cardTransactionRepo = Mockery::mock(CardTransactionRepository::class)->makePartial();

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
        'gateway' => GatewayEnum::KORAPAY->value,
    ]);

    $this->transaction = TransactionKoraCardPayment::factory()->create([
        'order_payment_reference' => $this->orderPayment->reference,
    ]);

    ApiLogsKoraCardPayment::factory()->create([
        'transaction_id' => $this->transaction->id,
    ]);

    $this->paymentAuthorizationDto = new PaymentAuthorizationDto(
        reference: $this->orderPayment->reference,
        authorization: [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'number' => fake()->creditCardNumber(),
            'cvv' => '123',
            'expiryMonth' => fake()->month(),
            'expiryYear' => '30',
            'pin' => '1234',
        ],
    );

    $this->authorizeOrderPayment = new AuthorizeOrderPaymentAction(
        $this->orderItemRepo,
        $this->orderPaymentRepo,
        $this->cardTransactionRepo
    );
});

describe('Authorize Order Payment', function () {
    it('should throw an exception if order payment does not exist', function () {
        $this->paymentAuthorizationDto->setReference('invalid_reference');

        $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);
    })->throws(ResourceNotFoundException::class,
        PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

    it('should throw an exception if order payment has already been completed', function () {
        $this->orderPayment->status = PaymentStatusEnum::SUCCESS->value;
        $this->orderPayment->save();

        $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);
    })->throws(ConflictHttpException::class,
        PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value);

    it('should return a failed status if transaction is not found', function () {
        $this->transaction->order_payment_reference = 'invalid_reference';
        $this->transaction->save();

        $response = $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBeNull()
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value)
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should return a failed status if transaction has already been completed', function () {
        $this->transaction->status = PaymentStatusEnum::SUCCESS->value;
        $this->transaction->save();

        $response = $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe($this->transaction->auth_model)
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value)
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should return a failed status if authorization charge fails', function () {
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Authorization fails. Try again',
            ]),
        ]);

        $response = $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe($this->transaction->auth_model)
            ->and($response->getResponseMessage())->toBe('Authorization fails. Try again')
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should successfully authorize a card', function () {
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charged successfully',
                'data' => [
                    'amount' => $this->orderPayment->order_amount,
                    'amount_charged' => $this->orderPayment->order_amount,
                    'auth_model' => AuthModelEnum::PIN->value,
                    'currency' => CurrencyEnum::NGN,
                    'fee' => 0,
                    'vat' => 0,
                    'response_message' => 'Card charged successfully',
                    'payment_reference' => 'KPY-12345-ref',
                    'status' => 'success',
                    'transaction_reference' => 'KPY-1234-ref',
                ],
            ]),
        ]);

        $response = $this->authorizeOrderPayment->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::PIN->value)
            ->and($response->getResponseMessage())->toBe('Card charged successfully')
            ->and($response->getAmountCharged())->toBe((float) $this->orderPayment->order_amount);
    });
});
