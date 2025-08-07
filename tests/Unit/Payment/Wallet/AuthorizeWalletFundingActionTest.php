<?php

namespace Tests\Unit\Payment;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Payment\Actions\Wallet\AuthorizeWalletFundingAction;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Payment\CardTransactionRepository;
use App\Infrastructure\Repositories\Payment\Wallet\WalletTransactionRepository;
use Illuminate\Support\Facades\Http;
use Mockery;

beforeEach(function () {
    $this->walletRepo = Mockery::mock(WalletRepositoryInterface::class);
    $this->walletTransactionRepo = Mockery::mock(WalletTransactionRepository::class)->makePartial();
    $this->cardTransactionRepo = Mockery::mock(CardTransactionRepository::class)->makePartial();

    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');

    $this->wallet = Wallet::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->walletTransaction = WalletTransaction::factory()->create([
        'wallet_id' => $this->wallet->id,
        'gateway' => GatewayEnum::KORAPAY->value,
    ]);

    $this->transaction = TransactionKoraCardPayment::factory()->create([
        'order_payment_reference' => $this->walletTransaction->reference,
    ]);

    ApiLogsKoraCardPayment::factory()->create([
        'transaction_id' => $this->transaction->id,
    ]);

    $this->paymentAuthorizationDto = new PaymentAuthorizationDto(
        reference: $this->walletTransaction->reference,
        authorization: [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'number' => fake()->creditCardNumber(),
            'cvv' => '123',
            'expiryMonth' => fake()->month(),
            'expiryYear' => '30',
            'pin' => '1234',
        ],
    );

    $this->authorizeWalletFunding = new AuthorizeWalletFundingAction(
        $this->walletRepo,
        $this->walletTransactionRepo,
        $this->cardTransactionRepo
    );
});

describe('Authorize Wallet Funding', function () {
    it('should throw an exception if wallet transaction does not exist', function () {
        $this->paymentAuthorizationDto->setReference('invalid_reference');

        $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);
    })->throws(ResourceNotFoundException::class,
        PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

    it('should throw an exception if wallet transaction has already been completed', function () {
        $this->walletTransaction->status = PaymentStatusEnum::SUCCESS->value;
        $this->walletTransaction->save();

        $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);
    })->throws(ConflictHttpException::class,
        PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value);

    it('should return a failed status from gateway if wallet transaction is not found', function () {
        $this->transaction->order_payment_reference = 'invalid_reference';
        $this->transaction->save();

        $response = $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBeNull()
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value)
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should return a failed status from gateway if transaction has already been completed', function () {
        $this->transaction->status = PaymentStatusEnum::SUCCESS->value;
        $this->transaction->save();

        $response = $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe($this->transaction->auth_model)
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value)
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should return a failed status from gateway if authorization charge fails', function () {
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Authorization fails. Try again',
            ]),
        ]);

        $response = $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe($this->transaction->auth_model)
            ->and($response->getResponseMessage())->toBe('Authorization fails. Try again')
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should successfully authorize a card from gateway ', function () {
        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charged successfully',
                'data' => [
                    'amount' => $this->walletTransaction->amount,
                    'amount_charged' => $this->walletTransaction->amount,
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

        $response = $this->authorizeWalletFunding->execute($this->paymentAuthorizationDto);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::PIN->value)
            ->and($response->getResponseMessage())->toBe('Card charged successfully')
            ->and($response->getAmountCharged())->toBe((float) $this->walletTransaction->amount);
    });
});
