<?php

namespace App\Domain\Order\Resources\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Shipping\Enums\DeliveryTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    use UtilitiesTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'status' => $this->status,
            'failure_reason' => $this->when($this->payment, $this->failureReason()),
            'currency' => $this->currency,
            'reference' => $this->reference,
            'amount' => $this->when($this->payment, $this->getAmount($this->status)),
            'payment_method' => $this->when($this->payment, $this->payment?->payment_method),
            'delivery_type' => $this->shipping->delivery_type,
            'delivery_address' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::DOOR_DELIVERY->value,
                $this->shipping->delivery_address),
            'pickup_station_name' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::PICKUP_STATION->value,
                $this->shipping->pickup_station_name),
            'ordered_completed_on' => $this->when($this->orderSuccessful(), $this->parseDate($this->updated_at)),
            'estimated_delivery_start_date' => $this->parseDateOnly($this->shipping->estimated_delivery_start_date),
            'estimated_delivery_end_date' => $this->parseDateOnly($this->shipping->estimated_delivery_end_date),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }

    private function failureReason(): ?string
    {
        $isPaymentFailed = $this->payment && $this->status === PaymentStatusEnum::FAILED->value;

        if ($isPaymentFailed) {
            return $this->payment->narration;
        }

        return null;
    }

    private function getAmount(string $status): ?int
    {
        if (in_array($status, self::completedTransactionStatuses())) {
            return $this->payment?->amount_charged;
        }

        return $this->payment?->order_amount;
    }

    private function orderSuccessful(): bool
    {
        return $this->status == OrderStatusEnum::SUCCESS->value;
    }
}
