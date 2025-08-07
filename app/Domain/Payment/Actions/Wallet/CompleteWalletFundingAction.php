<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Dtos\Wallet\WalletFundingResponseDto;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Domain\Payment\Notifications\WalletFundedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompleteWalletFundingAction extends BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected WalletTransactionRepositoryInterface $walletTransactionRepository,
        private readonly WalletAuditLogRepositoryInterface $walletAuditLogRepository,
    ) {
        parent::__construct(
            $walletRepository,
            $walletTransactionRepository,
        );
    }

    public function execute(PaymentResponseDto $transactionResponse): WalletFundingResponseDto
    {
        $walletTransaction = $this->getValidWalletTransaction($transactionResponse->getReference());

        $this->updateWalletRecords($walletTransaction, $transactionResponse);

        if ($transactionResponse->getStatus() === PaymentStatusEnum::SUCCESS->value) {
            $walletTransaction->wallet->user->notify(new WalletFundedNotification($walletTransaction));
        }

        return new WalletFundingResponseDto(
            status: $walletTransaction->status,
            amount: $walletTransaction->amount,
            currency: $walletTransaction->wallet->currency,
            paymentMethod: $walletTransaction->payment_method,
            reference: $walletTransaction->reference,
        );
    }

    private function updateWalletRecords(
        Model $walletTransaction,
        PaymentResponseDto $transactionResponse,
    ): void {
        DB::transaction(function () use ($walletTransaction, $transactionResponse) {
            $this->walletTransactionRepository->updateColumns($walletTransaction, [
                'fee' => $transactionResponse->getFee(),
                'vat' => $transactionResponse->getVat(),
                'status' => $transactionResponse->getStatus(),
                'completed_at' => now()->toDateTimeString(),
            ]);

            $wallet = $walletTransaction->wallet;

            $newBalance = $wallet->balance + $walletTransaction->amount;

            $this->walletAuditLogRepository->create([
                'wallet_id' => $wallet->id,
                'transaction_id' => $walletTransaction->id,
                'previous_balance' => $wallet->balance,
                'new_balance' => $newBalance,
            ]);

            $this->walletRepository->incrementBalance($wallet, $walletTransaction->amount);
        });
    }

    public function updateTransactionStatus(PaymentResponseDto $transactionResponse): void
    {
        $walletTransaction = $this->getValidWalletTransaction($transactionResponse->getReference());

        $this->walletTransactionRepository->updateColumns($walletTransaction, [
            'status' => $transactionResponse->getStatus(),
            'completed_at' => $transactionResponse->getStatus() === PaymentStatusEnum::FAILED->value ? now()->toDateTimeString() : null,
        ]);
    }
}
