<?php

namespace App\Domain\Payment\Actions;

use App\Domain\Payment\Constants\PaymentCategoryEnum;
use App\Domain\Payment\Constants\PaymentTypeEnum;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Services\Payments\PaymentGatewayStrategy;

class InitiateOrderPaymentAction
{
    public function __construct(
        private readonly PaymentGatewayStrategy $gatewayStrategy,
        private readonly GatewayRepositoryInterface $gatewayRepository,
        private readonly GatewayTypeRepositoryInterface $gatewayTypeRepository,
    ) {}

    public function execute(OrderPayment $orderPayment): array
    {
        $orderPaymentDto = $this->buildOrderPaymentDto($orderPayment);
        $gatewaySlug = $this->getGatewaySlug($orderPaymentDto->getCurrency());
        $gatewayInstance = $this->gatewayStrategy->getGatewayInstance($gatewaySlug);

        return $gatewayInstance->initialize($orderPaymentDto);
    }

    private function buildOrderPaymentDto(OrderPayment $orderPayment): InitiateOrderPaymentDto
    {
        $initiatePaymentDto = new InitiateOrderPaymentDto(
            amount: 1200,
            currency: $orderPayment->order->currency,
            card: new CardData(
                name: 'Test card name',
                number: '5188513618552975',
                cvv: '123',
                expiryMonth: '09',
                expiryYear: '30',
                pin: '1234',
            ),
            customer: new CustomerData(
                email: 'testemail@gmail.com',
                name: 'Test name'
            ),
            redirectUrl: 'https://webhook.site/b3eb9e61-9c77-402f-ba5e-019966c8a982',
        );

        $initiatePaymentDto->setPaymentId($orderPayment->id);

        return $initiatePaymentDto;
    }

    private function getGatewaySlug(string $currency): string
    {
        $gatewayFilterData = new GatewayFilterData(
            type: PaymentTypeEnum::CARD->value,
            category: PaymentCategoryEnum::COLLECTION->value,
            currency: $currency
        );

        $gatewayType = $this->gatewayTypeRepository->findGatewayType($gatewayFilterData);

        $gateway = $this->gatewayRepository->findByColumn(
            Gateway::class,
            'id',
            $gatewayType->primary_gateway_id,
        );

        return $gateway->slug;
    }
}
