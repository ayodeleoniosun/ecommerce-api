<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Order\BaseOrderAction;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentCategoryEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Services\Payments\PaymentGateway;
use Illuminate\Database\Eloquent\Model;

class PayWithCardAction extends BaseOrderAction
{
    use UtilitiesTrait;

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
        protected GatewayRepositoryInterface $gatewayRepository,
        protected GatewayTypeRepositoryInterface $gatewayTypeRepository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {
        parent::__construct(
            $orderItemRepository,
            $orderPaymentRepository,
            $this->gatewayRepository,
            $this->gatewayTypeRepository,
        );
    }

    /**
     * @throws BadRequestException
     */
    public function execute(Order $order, array $cardData): PaymentResponseDto
    {
        $this->orderPaymentRepository->updateColumns($order->payment, [
            'payment_method' => PaymentTypeEnum::CARD->value,
        ]);

        $initiateCardPaymentDto = $this->buildCardPaymentDto($order, $cardData);

        $gateway = $this->getGateway($order->currency);

        $paymentGateway = PaymentGateway::make($gateway, $this->cardTransactionRepository);

        return $paymentGateway->initiate($initiateCardPaymentDto);
    }

    private function buildCardPaymentDto(Model $order, array $card): InitiateCardPaymentDto
    {
        $orderPayment = $order->payment;

        if (! $orderPayment || $orderPayment?->status === OrderStatusEnum::FAILED->value) {
            $orderPayment = $this->createOrderPayment($order);
        }

        $user = auth()->user();

        $initiateCardPaymentDto = new InitiateCardPaymentDto(
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
            redirectUrl: 'https://example.com',
        );

        $initiateCardPaymentDto->setPaymentId($orderPayment->id);

        return $initiateCardPaymentDto;
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
}
