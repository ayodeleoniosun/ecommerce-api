<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Payment\Actions\Wallet\PayWithWalletAction;
use App\Domain\Payment\Dtos\PaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentTypeEnum;

class InitiateOrderPaymentAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PayWithCardAction $payWithCard,
        private readonly PayWithWalletAction $payWithWallet,
    ) {}

    /**
     * @throws ResourceNotFoundException
     * @throws BadRequestException
     */
    public function execute(PaymentDto $orderPaymentDto): PaymentResponseDto
    {
        $order = $this->orderRepository->findPendingOrder(auth()->user()->id);

        throw_if(! $order, ResourceNotFoundException::class, 'You are yet to checkout');

        if ($orderPaymentDto->getPaymentMethod() === PaymentTypeEnum::WALLET->value) {
            return $this->payWithWallet->execute($order);
        }

        if ($orderPaymentDto->getPaymentMethod() === PaymentTypeEnum::CARD->value) {
            return $this->payWithCard->execute($order, $orderPaymentDto->getCardData());
        }
    }
}
