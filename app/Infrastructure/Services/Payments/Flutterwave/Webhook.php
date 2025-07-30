<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Dtos\Webhook\FlutterwaveWebhookDto;
use App\Domain\Payment\Enums\GatewayEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Events\PaymentWebhookCompleted;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Services\Payments\Korapay\UtilityTrait;
use App\Infrastructure\Services\Payments\PaymentGateway;
use Illuminate\Support\Facades\Log;

class Webhook
{
    use UtilitiesTrait, UtilityTrait;

    public function __construct(
        public readonly CardTransactionRepositoryInterface $cardTransactionRepository,
    ) {}

    /**
     * @throws BadRequestException
     * @throws ResourceNotFoundException
     */
    public function execute(FlutterwaveWebhookDto $webhookDto): void
    {
        $transaction = $this->cardTransactionRepository->findByColumn(
            model: TransactionFlutterwaveCardPayment::class,
            field: 'reference',
            value: $webhookDto->getTransactionReference(),
        );

        if (! $transaction) {
            Log::info('Transaction reference '.$webhookDto->getTransactionReference().' does not exist as a flutterwave transaction');

            throw new ResourceNotFoundException('Transaction does not exist.');
        }

        if ($webhookDto->getStatus() === PaymentStatusEnum::SUCCESSFUL->value) {
            $paymentGateway = PaymentGateway::make(GatewayEnum::FLUTTERWAVE->value, $this->cardTransactionRepository);
            $verifyResponse = $paymentGateway->verify($transaction->gateway_transaction_reference);

            PaymentWebhookCompleted::dispatch($verifyResponse);
        } else {
            $transaction = $this->cardTransactionRepository->update(
                model: TransactionFlutterwaveCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $webhookDto->getStatus(),
                    'gateway_response' => PaymentResponseMessageEnum::TRANSACTION_FAILED->value,
                ],
            );

            $paymentResponseDto = new PaymentResponseDto(
                status: $transaction->status,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $webhookDto->getTransactionReference(),
                responseMessage: $transaction->gateway_response,
            );

            PaymentWebhookCompleted::dispatch($paymentResponseDto);
        }
    }
}
