<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ConflictHttpException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Payment\Constants\PaymentErrorEnum;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Services\Payments\PaymentGateway;

class AuthorizePaymentAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
        private readonly OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {}

    /**
     * @throws BadRequestException
     * @throws ConflictHttpException
     */
    public function execute(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        $orderPayment = $this->orderPaymentRepository->findByColumn(
            OrderPayment::class,
            'gateway_reference',
            $paymentAuthorizationDto->getReference(),
        );

        throw_if(! $orderPayment, ResourceNotFoundException::class, PaymentErrorEnum::TRANSACTION_NOT_FOUND->value);

        if (in_array($orderPayment->order->status, self::completedTransactionStatuses()) ||
            in_array($orderPayment->status, self::completedTransactionStatuses())) {
            throw new ConflictHttpException(PaymentErrorEnum::TRANSACTION_ALREADY_COMPLETED->value);
        }

        $paymentGateway = PaymentGateway::make($orderPayment->gateway, $this->cardTransactionRepository);

        return $paymentGateway->authorize($paymentAuthorizationDto);
    }
}
