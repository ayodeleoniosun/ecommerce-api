<?php

namespace App\Domain\Payment\Actions\Card;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\BaseOrderAction;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Payment\Dtos\Card\CardData;
use App\Domain\Payment\Dtos\Card\CustomerData;
use App\Domain\Payment\Dtos\Card\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentCategoryEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Services\Payments\PaymentGateway;

class PayWithCardAction extends BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
        private readonly GatewayRepositoryInterface $gatewayRepository,
        private readonly GatewayTypeRepositoryInterface $gatewayTypeRepository,
    ) {
        parent::__construct(
            $orderItemRepository,
            $orderPaymentRepository,
        );
    }

    /**
     * @throws BadRequestException
     */
    public function execute(Order $order, array $cardData): PaymentResponseDto
    {
        $orderPayment = $this->createOrderPayment($order);

        $gateway = $this->getGateway($order->currency);
        $gatewayReference = self::generateGatewayReference($gateway);

        $this->orderPaymentRepository->updateColumns($orderPayment, [
            'payment_method' => PaymentTypeEnum::CARD->value,
            'gateway' => $gateway,
            'gateway_reference' => $gatewayReference,
        ]);

        $initiateCardPaymentDto = $this->buildCardPaymentDto($orderPayment, $cardData);

        $paymentGateway = PaymentGateway::make($gateway, $this->cardTransactionRepository);

        return $paymentGateway->initiate($initiateCardPaymentDto);
    }

    private function getGateway(string $currency): string
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

    private function buildCardPaymentDto(OrderPayment $orderPayment, array $card): InitiateCardPaymentDto
    {
        $user = auth()->user();

        return new InitiateCardPaymentDto(
            amount: $orderPayment->order_amount,
            currency: $orderPayment->order->currency,
            card: new CardData(
                name: $card['name'],
                number: $card['number'],
                cvv: $card['cvv'],
                expiryMonth: $card['expiry_month'],
                expiryYear: $card['expiry_year'],
                pin: $card['pin']
            ),
            customer: new CustomerData(
                email: $user->email,
                name: $user->fullname,
            ),
            redirectUrl: config("payment.gateways.{$orderPayment->gateway}.webhook_url"),
            orderPaymentReference: $orderPayment->reference,
            gatewayReference: $orderPayment->gateway_reference
        );
    }
}
