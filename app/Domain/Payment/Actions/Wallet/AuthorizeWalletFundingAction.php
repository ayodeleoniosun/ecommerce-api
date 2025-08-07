<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Services\Payments\PaymentGateway;

class AuthorizeWalletFundingAction extends BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected WalletTransactionRepositoryInterface $walletTransactionRepository,
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
    ) {
        parent::__construct(
            $walletRepository,
            $walletTransactionRepository,
        );
    }

    /**
     * @throws BadRequestException
     */
    public function execute(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        $walletTransaction = $this->getValidWalletTransaction($paymentAuthorizationDto->getReference());

        $paymentGateway = PaymentGateway::make($walletTransaction->gateway, $this->cardTransactionRepository);

        return $paymentGateway->authorize($paymentAuthorizationDto);
    }
}
