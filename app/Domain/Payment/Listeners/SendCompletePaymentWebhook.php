<?php

namespace App\Domain\Payment\Listeners;

use App\Domain\Order\Resources\Order\OrderResource;
use App\Domain\Payment\Actions\Order\CompleteOrderPaymentAction;
use App\Domain\Payment\Events\PaymentWebhookCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCompletePaymentWebhook implements ShouldQueue
{
    /**s
     * Create the event listener.
     */
    public function __construct(
        private readonly CompleteOrderPaymentAction $completeOrderPayment,
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentWebhookCompleted $event): OrderResource
    {
        return $this->completeOrderPayment->execute($event->paymentResponseDto);
    }
}
