<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use Illuminate\Database\Eloquent\Model;

class BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository,
    ) {}

    protected function getValidWalletTransaction(string $reference): Model
    {
        $walletTransaction = $this->walletTransactionRepository->findByColumn(
            WalletTransaction::class,
            'reference',
            $reference,
        );

        throw_if(! $walletTransaction, ResourceNotFoundException::class,
            PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value);

        $transactionAlreadyCompleted = in_array($walletTransaction->status, self::completedTransactionStatuses());

        throw_if($transactionAlreadyCompleted, ConflictHttpException::class,
            PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value);

        return $walletTransaction;
    }

    protected function getWallet(string $currency): Wallet
    {
        $userId = auth()->user()->id;

        $wallet = $this->walletRepository->find($userId, $currency);

        if (! $wallet) {
            $wallet = $this->walletRepository->create([
                'user_id' => $userId,
                'currency' => $currency,
            ]);
        }

        $wallet->refresh();

        return $wallet;
    }
}
