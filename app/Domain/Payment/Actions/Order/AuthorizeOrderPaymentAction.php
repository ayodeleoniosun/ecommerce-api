<?php

namespace App\Domain\Payment\Actions\Order;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\BaseOrderAction;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Services\Payments\PaymentGateway;

class AuthorizeOrderPaymentAction extends BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
    ) {
        parent::__construct(
            $orderItemRepository,
            $orderPaymentRepository,
        );
    }

    /**
     * @throws BadRequestException
     */
    public function execute(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        $orderPayment = $this->getValidOrderPayment($paymentAuthorizationDto->getReference());

        $paymentGateway = PaymentGateway::make($orderPayment->gateway, $this->cardTransactionRepository);

        return $paymentGateway->authorize($paymentAuthorizationDto);
    }
}
