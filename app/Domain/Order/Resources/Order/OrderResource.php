<?php

namespace App\Domain\Order\Resources\Order;

use App\Application\Shared\Enum\DeliveryTypeEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\PaymentStatusEnum;
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
            'failure_reason' => $this->when($this->status === PaymentStatusEnum::FAILED->value,
                $this->payments->last()->narration),
            'currency' => $this->currency,
            'reference' => $this->reference,
            'amount' => $this->getAmount($this->status),
            'delivery_type' => $this->shipping->delivery_type,
            'delivery_address' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::DOOR_DELIVERY->value,
                $this->shipping->delivery_address),
            'pickup_station_name' => $this->when($this->shipping->delivery_type === DeliveryTypeEnum::PICKUP_STATION->value,
                $this->shipping->pickup_station_name),
            'estimated_delivery_start_date' => $this->parseDateOnly($this->shipping->estimated_delivery_start_date),
            'estimated_delivery_end_date' => $this->parseDateOnly($this->shipping->estimated_delivery_end_date),
        ];
    }

    private function getAmount(string $status): int
    {
        if (in_array($status, self::completedTransactionStatuses())) {
            return $this->payments->last()->amount_charged;
        }

        return $this->payments->last()->order_amount;
    }
}
