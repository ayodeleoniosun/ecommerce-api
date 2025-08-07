<?php

namespace Tests\Unit\Payment;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Payment\Actions\Wallet\CompleteWalletFundingAction;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Dtos\Wallet\WalletFundingResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Notifications\WalletFundedNotification;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletAuditLog;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Payment\Wallet\WalletTransactionRepository;
use Illuminate\Support\Facades\Notification;
use Mockery;

beforeEach(function () {
    $this->walletRepo = Mockery::mock(WalletRepositoryInterface::class);
    $this->walletTransactionRepo = Mockery::mock(WalletTransactionRepository::class)->makePartial();
    $this->walletAuditLogRepo = Mockery::mock(WalletAuditLogRepositoryInterface::class);

    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');

    $this->wallet = Wallet::factory()->create([
        'user_id' => $this->user->id,
        'balance' => 1000,
    ]);

    $this->walletTransaction = WalletTransaction::factory()->create([
        'wallet_id' => $this->wallet->id,
        'gateway' => GatewayEnum::KORAPAY->value,
        'amount' => 1000,
        'payment_method' => PaymentTypeEnum::CARD->value,
    ]);

    $this->walletAuditLog = WalletAuditLog::factory()->create([
        'wallet_id' => $this->wallet->id,
        'transaction_id' => $this->walletTransaction->id,
        'previous_balance' => $this->wallet->balance,
        'new_balance' => $this->wallet->balance + $this->walletTransaction->amount,
    ]);

    $this->paymentResponseDto = new PaymentResponseDto(
        status: PaymentStatusEnum::SUCCESS->value,
        paymentMethod: PaymentTypeEnum::CARD->value,
        reference: $this->walletTransaction->reference,
        responseMessage: 'Payment successful',
        authModel: AuthModelEnum::PIN->value,
        gateway: GatewayEnum::KORAPAY->value,
        amountCharged: 1000,
        fee: 4,
        vat: 1,
    );

    $this->completeWalletFunding = new CompleteWalletFundingAction(
        $this->walletRepo,
        $this->walletTransactionRepo,
        $this->walletAuditLogRepo
    );
});

describe('Complete Wallet Funding', function () {
    it('should throw an exception if wallet transaction does not exist', function () {
        $this->paymentResponseDto->setReference('invalid_reference');

        $this->completeWalletFunding->execute($this->paymentResponseDto);
    })->throws(ResourceNotFoundException::class,
        PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

    it('should throw an exception if wallet transaction has already been completed', function () {
        $this->walletTransaction->status = PaymentStatusEnum::SUCCESS->value;
        $this->walletTransaction->save();

        $this->completeWalletFunding->execute($this->paymentResponseDto);
    })->throws(ConflictHttpException::class,
        PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value);

    it('should complete wallet funding successfully', function () {
        Notification::fake();

        $newBalance = $this->wallet->balance + $this->walletTransaction->amount;

        $this->walletRepo->shouldReceive('incrementBalance')
            ->once()
            ->andReturnNull();

        $this->walletAuditLogRepo->shouldReceive('create')
            ->once()
            ->with([
                'wallet_id' => $this->wallet->id,
                'transaction_id' => $this->walletTransaction->id,
                'previous_balance' => $this->wallet->balance,
                'new_balance' => $newBalance,
            ])->andReturn($this->walletAuditLog);

        $response = $this->completeWalletFunding->execute($this->paymentResponseDto);

        Notification::assertSentTo(
            $this->walletTransaction->wallet->user,
            WalletFundedNotification::class,
            function ($notification) use ($response) {
                return $notification->walletTransaction->reference === $response->getReference();
            });

        expect($response)->toBeInstanceOf(WalletFundingResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAmount())->toBe((float) $this->walletTransaction->amount)
            ->and($response->getReference())->toBe($this->walletTransaction->reference);
    });
});
