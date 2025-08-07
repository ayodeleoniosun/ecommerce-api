<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\BaseOrderAction;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Enums\WalletTransactionTypeEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletOrderPaymentRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use Illuminate\Support\Facades\DB;

class PayWithWalletAction extends BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository,
        private readonly WalletAuditLogRepositoryInterface $walletAuditLogRepository,
        private readonly WalletOrderPaymentRepositoryInterface $walletOrderPaymentRepository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {
        parent::__construct($orderItemRepository, $orderPaymentRepository);
    }

    /**
     * @throws BadRequestException
     */
    public function execute(Order $order): PaymentResponseDto
    {
        $this->orderPaymentRepository->updateColumns($order->payment, [
            'payment_method' => PaymentTypeEnum::WALLET->value,
        ]);

        $wallet = $this->getWallet($order->currency);

        $orderPayment = $this->getOrderPayment($order);

        $this->validateWalletBalance($wallet->balance, $orderPayment->order_amount);

        $walletTransaction = $this->createWalletTransactions($wallet, $orderPayment);

        return new PaymentResponseDto(
            status: OrderStatusEnum::SUCCESS->value,
            paymentMethod: PaymentTypeEnum::WALLET->value,
            reference: $walletTransaction->reference,
            responseMessage: PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value
        );
    }

    private function getWallet(string $currency): Wallet
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

    private function getOrderPayment(Order $order): OrderPayment
    {
        $orderPayment = $order->payment;

        if (! $orderPayment || $orderPayment?->status === OrderStatusEnum::FAILED->value) {
            $orderPayment = $this->createOrderPayment($order);
        }

        return $orderPayment;
    }

    /**
     * @throws BadRequestException
     */
    private function validateWalletBalance(int $balance, int $orderAmount): void
    {
        if ($balance < $orderAmount) {
            throw new BadRequestException('Insufficient funds');
        }
    }

    private function createWalletTransactions(Wallet $wallet, OrderPayment $orderPayment): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $orderPayment) {
            $walletTransaction = $this->walletTransactionRepository->create([
                'wallet_id' => $wallet->id,
                'amount' => $orderPayment->order_amount,
                'type' => WalletTransactionTypeEnum::DEBIT->value,
                'reference' => self::generateRandomCharacters('WAL-'),
            ]);

            $this->walletOrderPaymentRepository->create([
                'wallet_transaction_id' => $walletTransaction->id,
                'order_payment_id' => $orderPayment->id,
            ]);

            $newBalance = $wallet->balance - $orderPayment->order_amount;

            $this->walletAuditLogRepository->create([
                'wallet_id' => $wallet->id,
                'transaction_id' => $walletTransaction->id,
                'previous_balance' => $wallet->balance,
                'new_balance' => $newBalance,
            ]);

            $this->walletRepository->decrementBalance($wallet, $orderPayment->order_amount);

            return $walletTransaction;
        });
    }
}
