<?php

namespace App\Domain\Payment\Events;

use App\Domain\Payment\Dtos\PaymentResponseDto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentWebhookCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public PaymentResponseDto $paymentResponseDto)
    {
        //
    }
}
