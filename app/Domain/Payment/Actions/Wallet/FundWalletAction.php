<?php

namespace App\Domain\Payment\Actions\Wallet;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\CardData;
use App\Domain\Payment\Dtos\CustomerData;
use App\Domain\Payment\Dtos\FundWalletDto;
use App\Domain\Payment\Dtos\GatewayFilterData;
use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentCategoryEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Services\Payments\PaymentGateway;

class FundWalletAction extends BaseWalletAction
{
    use UtilitiesTrait;

    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        private readonly CardTransactionRepositoryInterface $cardTransactionRepository,
        private readonly WalletTransactionRepositoryInterface $walletTransactionRepository,
        private readonly WalletAuditLogRepositoryInterface $walletAuditLogRepository,
        private readonly GatewayRepositoryInterface $gatewayRepository,
        private readonly GatewayTypeRepositoryInterface $gatewayTypeRepository,
    ) {
        parent::__construct($walletRepository);
    }

    /**
     * @throws BadRequestException
     */
    public function execute(FundWalletDto $fundWalletDto): PaymentResponseDto
    {
        $currency = $fundWalletDto->getCurrency();
        $wallet = $this->getWallet($currency);

        $cardPaymentDto = $this->buildCardPaymentDto($fundWalletDto);
        $gateway = $this->getGateway($currency);

        $paymentGateway = PaymentGateway::make($gateway, $this->cardTransactionRepository);

        return $paymentGateway->initiate($cardPaymentDto);
    }

    private function buildCardPaymentDto(FundWalletDto $fundWalletDto): InitiateCardPaymentDto
    {
        $user = auth()->user();
        $card = $fundWalletDto->getCardData();

        return new InitiateCardPaymentDto(
            amount: $fundWalletDto->getAmount(),
            currency: $fundWalletDto->getCurrency(),
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
