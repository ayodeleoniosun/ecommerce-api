<?php

namespace App\Domain\Payment\Actions;

use App\Application\Shared\Enum\OrderStatusEnum;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Actions\Cart\BaseOrder;
use App\Domain\Order\Interfaces\OrderItemRepositoryInterface;
use App\Domain\Order\Interfaces\OrderPaymentRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Payment\Constants\PaymentCategoryEnum;
use App\Domain\Payment\Constants\PaymentTypeEnum;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CheckoutPaymentDto;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Services\Payments\PaymentGatewayStrategy;
use Illuminate\Database\Eloquent\Model;

class InitiateOrderPaymentAction extends BaseOrder
{
    use UtilitiesTrait;

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentGatewayStrategy $gatewayStrategy,
        private readonly GatewayRepositoryInterface $gatewayRepository,
        private readonly GatewayTypeRepositoryInterface $gatewayTypeRepository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected OrderPaymentRepositoryInterface $orderPaymentRepository,
    ) {
        parent::__construct($orderItemRepository, $orderPaymentRepository);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function execute(CheckoutPaymentDto $checkoutPaymentDto): array
    {
        $order = $this->orderRepository->findPendingOrder(auth()->user()->id);

        throw_if(! $order, ResourceNotFoundException::class, 'No order is in progress');

        $orderPaymentDto = $this->buildOrderPaymentDto($order, $checkoutPaymentDto);
        $gatewaySlug = $this->getGatewaySlug($orderPaymentDto->getCurrency());
        $gatewayInstance = $this->gatewayStrategy->getGatewayInstance($gatewaySlug);

        return $gatewayInstance->initialize($orderPaymentDto);
    }

    private function buildOrderPaymentDto(Model $order, CheckoutPaymentDto $checkoutPaymentDto): InitiateOrderPaymentDto
    {
        $orderPayment = $order->payment;

        if (! $orderPayment || $orderPayment?->status === OrderStatusEnum::FAILED->value) {
            $orderPayment = $this->createOrderPayment($order);
        }

        $card = $checkoutPaymentDto->getCardData();
        $user = auth()->user();

        $initiatePaymentDto = new InitiateOrderPaymentDto(
            amount: $orderPayment->order_amount,
            currency: $orderPayment->order->currency,
            card: new CardData(
                name: $card['name'],
                number: $card['number'],
                cvv: $card['cvv'],
                expiryMonth: $card['expiry_month'],
                expiryYear: $card['expiry_year'],
                pin: $card['pin'],
            ),
            customer: new CustomerData(
                email: $user->email,
                name: $user->fullname,
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
